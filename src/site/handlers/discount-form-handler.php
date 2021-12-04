<?php
header('Content-Type: application/json; charset=utf-8');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = json_decode(file_get_contents("php://input"), true);

    $address = $data['address'];
    $country = $data['country'];
    $phoneNumber = $data['phoneNumber'];
    $termsConfirmation = $data['termsConfirmation'];
    $userName = $data['userName'];

    $responseData = new stdClass();

    $responseData->name = $name;
    $responseData->age = $age;
    $responseData->city = $email;

    echo json_encode($responseData);
}
