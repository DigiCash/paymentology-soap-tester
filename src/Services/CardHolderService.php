<?php

namespace DigiCash\PaymentologySoapTester\Services;

use DigiCash\PaymentologySoapTester\FlexpayClient;
use SimpleXMLElement;
use Exception;

readonly class CardHolderService extends FlexpayService
{
    public function __construct(
        private FlexpayClient $client
    ) {}

    /**
     * Execute the getCard SOAP request and return parsed response data.
     * If $traceId is null, a random UUID v4 will be generated automatically.
     * @throws Exception
     */
    public function getCardholder(
        string $cardholderId,
        string $customerId
    ): array {
        $traceId  = $this->generateUuid();

        $username = htmlspecialchars($this->client->getUsername());
        $password = htmlspecialchars($this->client->getPassword());
        $cardholderId   = htmlspecialchars($cardholderId);
        $traceId  = htmlspecialchars($traceId);
        $customerId = htmlspecialchars($customerId);

        $bodyXml = <<<XML
        <v:getCardholder>
            <arg0>
                <clientUsername>{$username}</clientUsername>
                <clientPassword>{$password}</clientPassword>
                <traceId>{$traceId}</traceId>
                <customerId>$customerId</customerId>
                <cardholderId>{$cardholderId}</cardholderId>
            </arg0>
        </v:getCardholder>
XML;

        $rawXmlResponse = $this->client->sendSoapRequest($bodyXml);

        return $this->parseGetCardholderResponse($rawXmlResponse);
    }

    /**
     * Parse raw SOAP XML string into an associative array
     * @throws Exception
     */
    private function parseGetCardholderResponse(string $xmlString): array
    {
        $xml = new SimpleXMLElement($xmlString);
        $xml->registerXPathNamespace('soap', 'http://schemas.xmlsoap.org/soap/envelope/');
        $xml->registerXPathNamespace('ns2', 'http://ws.fnds.co.za/wsdl/v_2_0');

        $result = $xml->xpath('//ns2:getCardholderResponse/return');

        if (empty($result)) {
            throw new \RuntimeException('Unable to parse XML response body or return element missing.');
        }

        return json_decode(json_encode($result[0], JSON_THROW_ON_ERROR), true, 512, JSON_THROW_ON_ERROR);
    }
}
