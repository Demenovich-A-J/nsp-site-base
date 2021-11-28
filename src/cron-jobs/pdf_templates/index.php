<!DOCTYPE html>
<html lang="en">

<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

  <style>
    body {
      color: #000;
      font-family: firefly, DejaVu Sans, sans-serif;
      font-size: 14px;
      color: #757982;
    }

    html,
    body {
      height: 100%;
      margin: 0;
    }

    .nsp-header {
      background-color: #fff;
      height: 70px;
      border-bottom: 1px solid #9b9b9b;
      padding-top: 10px;
    }

    .nsp-header a,
    footer a {
      color: #5a5a5a;
    }

    .logo-image {
      max-width: 100%;
      max-height: 100%;
      height: 50px;
      padding-top: 15px;
      padding-left: 10px;
    }

    @page {
      margin: 0px;
    }

    table {
      border-collapse: collapse;
      border-spacing: 0;
    }

    .col1 {
      width: 33%;
      display: inline-block;
    }

    .col2 {
      width: 32%;
      display: inline-block;
      margin-bottom: 15px;
      text-align: center;
    }

    .col3 {
      width: 31%;
      display: inline-block;
      margin-bottom: 15px;
      text-align: right;
    }

    .nsp-price {
      padding-top: 10px;
      background-color: #fff;
      min-height: 100%;

      /* Equal to height of footer */
      /* But also accounting for potential margin-bottom of last child */
      margin-bottom: -70px;
    }

    .nsp-price table,
    .nsp-price th,
    .nsp-price td {
      border: 1px solid #494949;
    }

    .footer,
    .push {
      height: 70px;
    }

    .price-table {
      padding-top: 50px;
    }

    .price-table td {
      color: #383e4f;
      font-size: 14px;
    }
  </style>
</head>

<body>
  <header class="nsp-header">
    <div class="col1">
      <img class="logo-image" src="https://nspreseller.by/assets/logo-partner-ru.svg" />
    </div>
    <div class="col2">
      <table width="100%" style="text-align: center">
        <tr>
          <td>Дистрибьютор NSP</td>
        </tr>
        <tr>
          <td><b>Елена Деменович</b></td>
        </tr>
      </table>
    </div>
    <div class="col3">
      <table width="100%" style="text-align: right">
        <tr>
          <td><a href="tel:+375 (XXX) XX-XX-XX">+375 (XXX) XX-XX-XX</a></td>
        </tr>
        <tr>
          <td><a href="https://nspreseller.by/" target="_blank">www.nspreseller.by</a></td>
        </tr>
      </table>
    </div>
  </header>
  <div class="nsp-price">
    <h1 style="text-align: center; color: #007f6e">Прайс на продукцию NSP</h1>

    <?php
    $groupedById = array();

    foreach ($products as $product) {
      $groupedById[$product->CategoryId][] = $product;
    }

    foreach ($groupedById as $categoryId => $productsList) {
      $category = current(array_filter(
        $categories,
        fn ($v, $key) => $v->id == $categoryId,
        ARRAY_FILTER_USE_BOTH
      ));
    ?>
      <table class="price-table" width="80%" style="margin-left: 10%">
        <tr>
          <th colspan="3" style="
                text-align: center;
                font-size: 26px;
                background-color: #009d88;
                color: #fff;
              ">
            <?php echo $category->Name ?>
          </th>
        </tr>
        <?php
        foreach ($productsList as $product) {
          echo "<tr>";
          echo "<td>";
          echo "<p>$product->Name</p>";
          echo "<p>$product->NameEng</p>";
          echo "</td>";
          echo "<td style='text-align:center;'>$product->PV PV</td>";
          echo "<td style='text-align:center;'><b>$product->PriceUSD $</b></td>";
          echo "</tr>";
        }
        ?>
      </table>
    <?php
    }
    ?>
    <div class="push"></div>
  </div>
  <footer>
    <div class="col1">
      <img class="logo-image" src="https://nspreseller.by/assets/logo-partner-ru.svg" />
    </div>
    <div class="col2">
      <table width="100%" style="text-align: center">
        <tr>
          <td>Дистрибьютор NSP</td>
        </tr>
        <tr>
          <td><b>Елена Деменович</b></td>
        </tr>
      </table>
    </div>
    <div class="col3">
      <table width="100%" style="text-align: right">
        <tr>
          <td><a href="tel:+375 (XXX) XX-XX-XX">+375 (XXX) XX-XX-XX</a></td>
        </tr>
        <tr>
          <td><a href="https://nspreseller.by/" target="_blank">www.nspreseller.by</a></td>
        </tr>
      </table>
    </div>
  </footer>
</body>

</html>