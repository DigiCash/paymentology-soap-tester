<?php

use DigiCash\PaymentologySoapTester\FlexpayClient;
use DigiCash\PaymentologySoapTester\Services\CardService;

it('fetches card details via getCard request successfully', function () {
    $client = new FlexpayClient(
        endpointUrl: $_ENV['FLEXPAY_ENDPOINT'],
        clientUsername: $_ENV['FLEXPAY_USERNAME'],
        clientPassword: $_ENV['FLEXPAY_PASSWORD']
    );

    $cardService = new CardService($client);

    $cardId = $_ENV['TEST_CARD_ID'] ?? '3128028';
    $response = $cardService->getCard(cardId: $cardId);

    // Assert SOAP Response Structure
    expect($response)
        ->toBeArray()
        ->toHaveKey('responseCode')
        ->toHaveKey('responseMessage')
        ->toHaveKey('card');

    expect($response['responseCode'])->toBe('FNDS000001');
    expect($response['responseMessage'])->toBe('Success');
    expect($response['card']['cardId'])->toBe($cardId);
});
