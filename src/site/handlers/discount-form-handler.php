<?php
function SaveDiscountRequest($data)
{
    $servername = $_SERVER['DB_HOST'];
    $username = $_SERVER['DB_USER'];
    $password = $_SERVER['DB_PASS'];
    $dbname = $_SERVER['DB_NAME'];

    // Create connection
    $link = mysqli_connect($servername, $username, $password, $dbname);

    // Attempt insert query execution
    $sql = "INSERT INTO `discount-request`(`FullName`, `Country`, `Address`, `PhoneNumber`, `TermsConfirmed`) VALUES (?,?,?,?,?)";

    if ($stmt = mysqli_prepare($link, $sql)) {
        mysqli_stmt_bind_param($stmt, "ssssi", $param_full_name, $param_country, $param_address, $param_phone_number, $param_terms_confirmed);

        $param_full_name = $data['userName'];
        $param_country = $data['country'];
        $param_address = $data['address'];
        $param_phone_number = $data['phoneNumber'];
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

    SaveDiscountRequest($data);
} else {
    header("Location: /");
    exit();
}
