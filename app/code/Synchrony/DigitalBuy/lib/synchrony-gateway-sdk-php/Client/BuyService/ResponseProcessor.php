<?php

namespace Synchrony\Gateway\Client\BuyService;

class ResponseProcessor
{
    /**
     * Parse SOAP XML response and extract response object as array
     *
     * @param $response
     * @param $responseObjName
     * @param $responseParamName
     * @return array
     * @throws \Exception
     */
    public function process($response, $responseObjName, $responseParamName)
    {
        $curUseErrors = libxml_use_internal_errors(true);
        $responseXml = simplexml_load_string($response);
        if ($responseXml === false) {
            $errors = libxml_get_errors();
            libxml_use_internal_errors($curUseErrors);
            $error = $errors ? 'Unable to parse XML: ' . implode(', ', $errors) : 'Unable to parse XML';
            throw new \Exception($error);
        }
        libxml_use_internal_errors($curUseErrors);
        $responseArray = $this->xmlToArray($responseXml);

        if (isset($responseArray['Body'][$responseObjName][$responseParamName])) {
            $result = $responseArray['Body'][$responseObjName][$responseParamName];
        } elseif (isset($responseArray['Body']['Fault'])) {
            $result = $responseArray['Body']['Fault'];
        } else {
            throw new \Exception('Unknown XML structure');
        }

        return $result;
    }

    /**
     * Convert xml to array
     *
     * @param \SimpleXMLElement $xml
     * @return array
     */
    private function xmlToArray(\SimpleXMLElement $xml)
    {
        $data = [];
        $namespaces = [null] + $xml->getDocNamespaces(true);
        foreach ($namespaces as $namespace) {
            foreach ($xml->children($namespace) as $name => $element) {
                if (!($value = trim($element))) {
                    $value = $this->xmlToArray($element);
                }
                $data[$name] = $value;
            }
        }
        return $data;
    }
}
