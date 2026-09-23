<?php

namespace DigiCash\PaymentologySoapTester;

use Exception;

readonly class FlexpayClient
{
    public function __construct(
        private string $endpointUrl,
        private string $clientUsername,
        private string $clientPassword,
        private int    $timeout = 30
    )
    {
    }

    /**
     * Wrap payload body in standard SOAP envelope and send via cURL
     */
    public function sendSoapRequest(string $bodyXml): string
    {
        $envelope = <<<XML
<?xml version="1.0" encoding="UTF-8"?>
<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:v="http://ws.fnds.co.za/wsdl/v_2_0">
    <soapenv:Header/>
    <soapenv:Body>
        {$bodyXml}
    </soapenv:Body>
</soapenv:Envelope>
XML;

        $ch = curl_init($this->endpointUrl);
        curl_setopt_array($ch, [
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $envelope,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => [
                'Content-Type: text/xml; charset=utf-8',
                'SOAPAction: ""',
                'Content-Length: ' . strlen($envelope),
            ],
            CURLOPT_TIMEOUT => $this->timeout,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $errno = curl_errno($ch);
        $error = curl_error($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($errno) {
            throw new Exception("cURL Error ({$errno}): {$error}");
        }

        if ($httpCode >= 400) {
            throw new Exception("HTTP Status Error: Received code {$httpCode}");
        }

        return $response;
    }

    public function getUsername(): string
    {
        return $this->clientUsername;
    }

    public function getPassword(): string
    {
        return $this->clientPassword;
    }
}
