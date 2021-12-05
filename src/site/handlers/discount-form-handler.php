<?php
require_once 'services/google-sheet-service.php';

function SaveDiscountRequest($data)
{
    $servername = $_SERVER['DB_HOST'];
    $username = $_SERVER['DB_USER'];
    $password = $_SERVER['DB_PASS'];
    $dbname = $_SERVER['DB_NAME'];

    // Create connection
    $link = mysqli_connect($servername, $username, $password, $dbname);

    // Attempt insert query execution
    $sql = "INSERT INTO `discount-request`(`FullName`, `Country`, `Address`, `PhoneNumber`, `TermsConfirmed`, `RequestDate`) VALUES (?,?,?,?,?,?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssis", $param_full_name, $param_country, $param_address, $param_phone_number, $param_terms_confirmed, $param_request_date);

        $param_full_name = $data['userName'];
        $param_country = $data['country'];
        $param_address = $data['address'];
        $param_phone_number = $data['phoneNumber'];
        $param_request_date = $data['requestDate'];
        $param_terms_confirmed = true;

        if (mysqli_stmt_execute($stmt)) {
            print_r("Records inserted successfully.");
        } else {
            print_r("ERROR: Could not able to execute $sql. " . mysqli_error($link));
        }
    }

    mysqli_close($link);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    header('Content-Type: application/json; charset=utf-8');

    $data = json_decode(file_get_contents("php://input"), true);

    date_default_timezone_set('Europe/Moscow');
    $data['requestDate'] = date('Y-m-d H:i:s');

    SaveDiscountRequest($data);

    SaveDiscountRequestToGoogle([$data['userName'], $data['country'], $data['address'], $data['phoneNumber'], $data['requestDate'], 'НЕ ОБРАБОТАНО']);
} else {
    header("Location: /");
    exit();
}
