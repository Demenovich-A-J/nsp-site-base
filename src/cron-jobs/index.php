<?php
header('Content-Type: text/html; charset=cp1251');

require 'vendor/autoload.php';

use PHPHtmlParser\Dom;
use Dompdf\Dompdf;
use Dompdf\Options;

function GetItemInfo($itemContainer)
{
  $name = '';
  $nameEng = '';
  $sku = '';
  $url = '';

  $url = $itemContainer->href;
  $name = $itemContainer->find("strong")->innerText;
  $nameEng = $itemContainer->find("span")->innerText;
  $sku = $itemContainer->find("p > span")->innerText;

  $obj = (object)array('name' => $name, 'nameEng' => $nameEng, 'sku' => $sku, 'url' => $url);
  return $obj;
}

function GetItemsInfo($url)
{
  $dom = new Dom;
  $dom->loadFromUrl($url);

  $itemsContainer = $dom->find(".list-product > li > a");

  $itemsInfo = array();
  foreach ($itemsContainer as $itemContainer) {
    $itemInfo = GetItemInfo($itemContainer);
    array_push($itemsInfo, $itemInfo);
  }

  return $itemsInfo;
}

function ScrapItemsFromCategory($urlBase)
{
  $queryTemplate = '?sort=create_time.desc&page=';
  $itemsInfo = array();

  for ($i = 1; $i < 8; $i++) {
    $itemInfo = GetItemsInfo("$urlBase$queryTemplate$i");

    foreach ($itemInfo as $item) {
      array_push($itemsInfo, $item);
    }
  }

  return $itemsInfo;
}

function GetCategories()
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "a92750_db";
  $d = "";

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  $sql = "SELECT id, ParentCategoryId, Name, SourceSiteUrl FROM category";
  $categories = array();

  if ($result = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_array($result)) {
        $category = (object)array('id' => $row['id'], 'ParentCategoryId' => $row['ParentCategoryId'], 'Name' => $row['Name'], 'SourceSiteUrl' => $row['SourceSiteUrl']);
        array_push($categories, $category);
      }
      mysqli_free_result($result);
    } else {
      echo "No records matching your query were found.";
    }
  }
  // Close connection
  mysqli_close($conn);

  return $categories;
}

function SaveItem($link, $item, $categoryId)
{
  // Attempt insert query execution
  $sql = "INSERT INTO `product`(`name`, `nameEng`, `sku`, `url`, `categoryId`) VALUES (?,?,?,?, ?)";

  if ($stmt = mysqli_prepare($link, $sql)) {
    mysqli_stmt_bind_param($stmt, "ssssd", $param_name, $param_name_eng, $param_sku, $param_url, $param_categoryId);

    $param_name = $item->name;
    $param_name_eng = $item->nameEng;
    $param_sku = $item->sku;
    $param_url = $item->url;
    $param_categoryId = $categoryId;

    if (mysqli_stmt_execute($stmt)) {
      print_r("Records inserted successfully.");
    } else {
      print_r("ERROR: Could not able to execute $sql. " . mysqli_error($link));
    }
  }
}

function IsItemExists($link, $name)
{
  $sql = "SELECT 1 FROM `product` WHERE name='$name' LIMIT 1";

  $result = mysqli_query($link, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    return true;
  } else {
    return false;
  }
}

function SaveItemsToDatabase($items, $categoryId)
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "a92750_db";
  // Create connection
  $link = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  print_r(count($items));

  foreach ($items as $item) {
    if (!IsItemExists($link, $item->name)) {
      SaveItem($link, $item, $categoryId);
    }
  }

  // Close connection
  mysqli_close($link);

  print_r("Done!");
}

function ScrapAllItemsByCategory()
{
  $categories = GetCategories();

  foreach ($categories as $category) {
    $urlBase = $category->SourceSiteUrl;

    $items = ScrapItemsFromCategory($urlBase);

    SaveItemsToDatabase($items, $category->id);
  }
}

function GetBADPrices()
{
  $dom = new Dom;
  $dom->loadFromUrl('https://www.sunprod.ru/goodies.php');

  $itemsContainer = $dom->find("body table")[7];
  $itemRows = $itemsContainer->find("tr");

  $itemsPriceInfo = array();

  foreach ($itemRows as $itemRow) {
    $itemColumns = $itemRow->find("td");
    if (count($itemColumns) > 0 && $itemColumns[0]->getAttribute('class') == "gr4") {
      if (count($itemColumns[1]->find("h4")) != 0) {
        $pvPriceContainer = $itemColumns[1]->find("p");

        $sku = (int) filter_var($itemColumns[0]->innerText, FILTER_SANITIZE_NUMBER_INT);
        $title = $itemColumns[1]->find("h4")->innerText;
        $price = (float) filter_var(str_replace(",", ".", $itemColumns[2]->innerText), FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $pvPrice = '???';

        if (count($pvPriceContainer) != 0) {
          $pvPrice = $pvPriceContainer->innerText;
          $pvPrice = str_replace(",", ".", $pvPrice);
          $pvPrice = (float) filter_var($pvPrice, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        }

        array_push($itemsPriceInfo, (object)array('sku' => $sku, 'title' => $title, 'price' => $price, 'pvPrice' => $pvPrice));
      }
    }
  }

  return $itemsPriceInfo;
}

//ScrapAllItemsByCategory();

function UpdatePrices()
{
  $prices = GetBADPrices();

  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "a92750_db";
  // Create connection
  $link = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if ($link === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  foreach ($prices as $price) {
    $sql = "UPDATE `product` SET priceUSD=?, pv=? WHERE SKU=?";

    if ($stmt = mysqli_prepare($link, $sql)) {
      mysqli_stmt_bind_param($stmt, "dds", $param_priceUSD, $param_pv, $param_sku);

      $param_priceUSD = $price->price;
      $param_pv = $price->pvPrice;
      $param_sku = $price->sku;

      if (mysqli_stmt_execute($stmt)) {
        print_r("Records updated successfully.");
      } else {
        print_r("ERROR: Could not able to execute $sql. " . mysqli_error($link));
      }
    }
  }
}

//UpdatePrices();

function render_php($path, $vars = null)
{
  if (is_array($vars) && !empty($vars)) {
    extract($vars);
  }
  ob_start();
  include($path);
  $var = ob_get_contents();
  ob_end_clean();
  return $var;
}

function GetProducts()
{
  $servername = "localhost";
  $username = "root";
  $password = "";
  $dbname = "a92750_db";
  $d = "";

  // Create connection
  $conn = mysqli_connect($servername, $username, $password, $dbname);

  // Check connection
  if ($conn === false) {
    die("ERROR: Could not connect. " . mysqli_connect_error());
  }

  $sql = "SELECT `Id`, `Name`, `NameEng`, `SKU`, `URL`, `CategoryId`, `PV`, `PriceUSD`, `CategoryId` FROM `product`";
  $products = array();

  if ($result = mysqli_query($conn, $sql)) {
    if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_array($result)) {
        $product = (object)array('id' => $row['Id'], 'Name' => $row['Name'], 'NameEng' => $row['NameEng'], 'SKU' => $row['SKU'], 'PV' => $row['PV'], 'PriceUSD' => $row['PriceUSD'], 'CategoryId' => $row['CategoryId']);
        array_push($products, $product);
      }
      mysqli_free_result($result);
    } else {
      echo "No records matching your query were found.";
    }
  }
  // Close connection
  mysqli_close($conn);

  return $products;
}

function GeneratePdf()
{
  $products = GetProducts();
  $categories = GetCategories();

  print_r(count($products));

  $file = render_php('./pdf_templates/index.php', array("products" => $products, 'categories' => $categories));

  $options = new Options();
  $options->setIsHtml5ParserEnabled(true);
  $options->set('defaultFont', 'DejaVu Serif');
  $options->set('isRemoteEnabled', true);

  $dompdf = new Dompdf($options);

  $dompdf->loadHtml($file, 'UTF-8');

  // Render the HTML as PDF
  $dompdf->render();

  $dompdf->render();
  $output = $dompdf->output();
  file_put_contents('NSP-price.pdf', $output);
}

GeneratePdf();
?>

<html>

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=windows-1251">
</head>

<body>
  <p>test</p>
</body>

</html>