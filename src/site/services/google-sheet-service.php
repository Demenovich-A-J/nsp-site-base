<?php

function SaveDiscountRequestToGoogle($data)
{
    $client = new Google\Client();

    $client->setAuthConfig($_SERVER["DOCUMENT_ROOT"] . '/service-account.json');
    $client->setApplicationName('Google Sheets and PHP');
    $client->setAccessType('offline');
    $client->addScope(Google\Service\Sheets::SPREADSHEETS);

    $service = new Google\Service\Sheets($client);


    $spreadsheetId = $_SERVER['SPREAD_SHEET_ID']; //It is present in your URL

    $update_range = "A:E";
    $values = [$data];

    $body = new Google\Service\Sheets\ValueRange([
        'values' => $values
    ]);
    $params = [
        'valueInputOption' => 'USER_ENTERED'
    ];

    $service->spreadsheets_values->append($spreadsheetId, $update_range, $body, $params);
}
