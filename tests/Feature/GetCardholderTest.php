<?php

use DigiCash\PaymentologySoapTester\FlexpayClient;
use DigiCash\PaymentologySoapTester\Services\CardHolderService;

it(/**
 * @throws Exception
 */ /**
 * @throws Exception
 */ /**
 * @throws Exception
 */ 'fetches card details via getCardholder request successfully', function () {
    $client = new FlexpayClient(
        endpointUrl: $_ENV['FLEXPAY_ENDPOINT'],
        clientUsername: $_ENV['FLEXPAY_USERNAME'],
        clientPassword: $_ENV['FLEXPAY_PASSWORD']
    );

    $cardholderService = new CardHolderService($client);

    $cardholderId = $_ENV['TEST_CARD_HOLDER_ID'] ?? '1419357';
    $customerId = $_ENV['FLEXPAY_CUSTOMER_ID'] ?? '1902';
    try {
        $response = $cardholderService->getCardholder(cardholderId: $cardholderId, customerId: $customerId);
    } catch (Throwable $e) {
        print "<pre>";
        print_r($e->getMessage());
        print "</pre>";
    }


    // Assert SOAP Response Structure
    expect($response)
        ->toBeArray()
        ->toHaveKey('responseCode')
        ->toHaveKey('responseMessage')
        ->toHaveKey('cardholder')
        ->and($response['responseCode'])->toBe('FNDS000001')
        ->and($response['responseMessage'])->toBe('Success')
        ->and($response['cardholder']['cardholderId'])->toBe($cardholderId);

});
