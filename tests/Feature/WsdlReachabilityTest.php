<?php

it('can successfully reach and retrieve the WSDL definition', function () {
    $endpointUrl = $_ENV['FLEXPAY_ENDPOINT'];

    // Ensure the URL ends with ?wsdl
    $wsdlUrl = str_contains($endpointUrl, '?wsdl')
        ? $endpointUrl
        : $endpointUrl . '?wsdl';

    $ch = curl_init($wsdlUrl);
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_TIMEOUT        => 15,
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_USERAGENT      => 'PaymentologySoapTester/1.0',
    ]);

    $response = curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    // Assert network connectivity succeeded with no cURL errors
    expect($curlError)->toBeEmpty();

    // Assert HTTP status code 200
    expect($httpCode)->toBe(200);

    // Assert the content contains WSDL XML definitions
    expect($response)
        ->toBeString()
        ->toContain('definitions');
});
