<?php

namespace DigiCash\PaymentologySoapTester\Services;

use DigiCash\PaymentologySoapTester\FlexpayClient;
use SimpleXMLElement;
use Exception;

class CardService
{
    public function __construct(
        private readonly FlexpayClient $client
    ) {}

    /**
     * Execute the getCard SOAP request and return parsed response data.
     * If $traceId is null, a random UUID v4 will be generated automatically.
     */
    public function getCard(
        string $cardId,
        ?string $traceId = null,
        string $sessionKey = ''
    ): array {
        $traceId  = $traceId ?? $this->generateUuid();

        $username = htmlspecialchars($this->client->getUsername());
        $password = htmlspecialchars($this->client->getPassword());
        $cardId   = htmlspecialchars($cardId);
        $traceId  = htmlspecialchars($traceId);

        $bodyXml = <<<XML
        <v:getCard>
            <arg0>
                <clientUsername>{$username}</clientUsername>
                <clientPassword>{$password}</clientPassword>
                <traceId>{$traceId}</traceId>
                <sessionKey>{$sessionKey}</sessionKey>
                <cardId>{$cardId}</cardId>
            </arg0>
        </v:getCard>
XML;

        $rawXmlResponse = $this->client->sendSoapRequest($bodyXml);

        return $this->parseGetCardResponse($rawXmlResponse);
    }

    /**
     * Generate a cryptographically secure RFC 4122 compliant UUID v4
     */
    private function generateUuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Parse raw SOAP XML string into an associative array
     */
    private function parseGetCardResponse(string $xmlString): array
    {
        $xml = new SimpleXMLElement($xmlString);
        $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ns2', 'http://ws.fnds.co.za/wsdl/v_2_0');

        $result = $xml->xpath('//ns2:getCardResponse/return');

        if (empty($result)) {
            throw new Exception('Unable to parse XML response body or return element missing.');
        }

        return json_decode(json_encode($result[0]), true);
    }
}
