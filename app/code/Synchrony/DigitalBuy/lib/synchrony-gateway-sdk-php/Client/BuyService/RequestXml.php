<?php

namespace Synchrony\Gateway\Client\BuyService;

/**
 * SOAP body builder
 * @package Synchrony\Gateway\Client
 */
class RequestXml
{
    const XMLNS = 'xmlns';
    const SOAP_ENVELOPE_NAMESPACE = 'soapenv';
    const REQUEST_NAMESPACE = 'v2';
    const REQUEST_PARAM_NAMESPACE = 'java';
    const WSSSECURITY_SECRET_NAMESPACE = 'wsse';
    const WSSSECURITY_UTILITY_NAMESPACE = 'wsu';

    /**
     * API Username
     *
     * @var string
     */
    private $apiUserName;

    /**
     * API Password
     *
     * @var string
     */
    private $apiPassword;

    /**
     * @var \DOMDocument
     */
    private $dom;

    /**
     * @var \DOMElement
     */
    private $rootElement;

    /**
     * @var \DOMElement
     */
    private $soapHeader;

    /**
     * @var \DOMElement
     */
    private $soapBody;

    /**
     * Namespace urls
     *
     * @var array
     */
    protected $namespaceUrls = [
        self::XMLNS => 'http://www.w3.org/2000/xmlns/',
        self::SOAP_ENVELOPE_NAMESPACE => 'http://schemas.xmlsoap.org/soap/envelope/',
        self::REQUEST_PARAM_NAMESPACE => 'http://schemas.syf.com/service/buy/java',
        self::REQUEST_NAMESPACE => 'http://schemas.syf.com/services/V2',
        self::WSSSECURITY_SECRET_NAMESPACE => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-secext-1.0.xsd',
        self::WSSSECURITY_UTILITY_NAMESPACE => 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-wssecurity-utility-1.0.xsd'
    ];

    /**
     * @param string $apiUsername
     * @param string $apiPassword
     */
    public function __construct($apiUsername, $apiPassword)
    {
        $this->apiUsername = $apiUsername;
        $this->apiPassword = $apiPassword;
    }

    /**
     * Get DOM document
     *
     * @return DOMDocument
     */
    public function getDomDocument()
    {
        return $this->dom;
    }

    /**
     * Get document element from DOM
     *
     * @return DOMElement
     */
    public function getRootElement()
    {
        return $this->dom->documentElement;
    }

    /**
     * Get Soap Request Payload
     * @param array $requestParameters
     * @param string $requestName
     * @param string $requestParamEncloserName
     *
     * @return string
     */
    public function getSoapRequestXml(array $requestParameters, $requestName, $requestParamEncloserName)
    {
        if ($requestParamEncloserName === null) {
            $requestParamEncloserName = $requestName;
        }

        $this->init()
            ->addWSSecuritySoapHeader()
            ->addServiceRequestBodyElement($requestParameters, $requestName, $requestParamEncloserName);

        return $this->dom->saveXML($this->getRootElement());
    }

    /**
     * Creates Soap XMl skeleton
     *
     * @return $this
     */
    private function init()
    {
        $this->dom = new \DOMDocument();
        $this->rootElement = $this->dom->createElementNS(
            $this->namespaceUrls[self::SOAP_ENVELOPE_NAMESPACE],
            self::SOAP_ENVELOPE_NAMESPACE . ':Envelope'
        );

        $this->dom->appendChild($this->rootElement);

        $this->soapHeader = $this->dom->createElement(self::SOAP_ENVELOPE_NAMESPACE . ':Header');
        $this->rootElement->appendChild($this->soapHeader);

        $this->soapBody = $this->dom->createElement(self::SOAP_ENVELOPE_NAMESPACE . ':Body');
        $this->rootElement->appendChild($this->soapBody);

        return $this;
    }

    /**
     * Adds WSS Security Header to Soap xml Header
     *
     * @return $this
     */
    private function addWSSecuritySoapHeader()
    {
        $securityHeader = $this->dom->createElement(
            self::WSSSECURITY_SECRET_NAMESPACE . ':Security'
        );
        $securityHeader->setAttributeNS(
            $this->namespaceUrls[self::XMLNS],
            self::XMLNS . ':' . self::WSSSECURITY_SECRET_NAMESPACE,
            $this->namespaceUrls[self::WSSSECURITY_SECRET_NAMESPACE]
        );
        $securityHeader->setAttributeNS(
            $this->namespaceUrls[self::XMLNS],
            self::XMLNS . ':' . self::WSSSECURITY_UTILITY_NAMESPACE,
            $this->namespaceUrls[self::WSSSECURITY_UTILITY_NAMESPACE]
        );
        $securityHeader->setAttribute(self::SOAP_ENVELOPE_NAMESPACE . ':mustUnderstand', 1);
        $this->soapHeader->appendChild($securityHeader);

        $userNameToken = $this->dom->createElement(self::WSSSECURITY_SECRET_NAMESPACE . ':UsernameToken');
        $securityHeader->appendChild($userNameToken);

        $username = $this->dom->createElement(self::WSSSECURITY_SECRET_NAMESPACE . ':Username', $this->apiUsername);
        $userNameToken->appendChild($username);

        $password = $this->dom->createElement(self::WSSSECURITY_SECRET_NAMESPACE . ':Password', $this->apiPassword);
        $password->setAttribute('Type', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-username-token-profile-1.0#PasswordText');
        $userNameToken->appendChild($password);

        $curdate = gmdate('Y-m-d\TH:i:s.u\Z');

        $nonce = $this->dom->createElement(self::WSSSECURITY_SECRET_NAMESPACE . ':Nonce', $this->getNonce($curdate));
        $nonce->setAttribute('EncodingType', 'http://docs.oasis-open.org/wss/2004/01/oasis-200401-wss-soap-message-security-1.0#Base64Binary');
        $userNameToken->appendChild($nonce);

        $created = $this->dom->createElement(self::WSSSECURITY_UTILITY_NAMESPACE . ':Created', $curdate);
        $userNameToken->appendChild($created);

        return $this;
    }

    /**
     * @param $requestParameters
     * @param $requestName
     * @param $requestParamEncloserName
     * @return $this
     */
    private function addServiceRequestBodyElement($requestParameters, $requestName, $requestParamEncloserName)
    {
        $this->rootElement->setAttributeNS(
            $this->namespaceUrls[self::XMLNS],
            self::XMLNS . ':' . self::REQUEST_NAMESPACE,
            $this->namespaceUrls[self::REQUEST_NAMESPACE]
        );
        $this->rootElement->setAttributeNS(
            $this->namespaceUrls[self::XMLNS],
            self::XMLNS . ':' . self::REQUEST_PARAM_NAMESPACE,
            $this->namespaceUrls[self::REQUEST_PARAM_NAMESPACE]
        );

        $requestNode = $this->dom->createElement(self::REQUEST_NAMESPACE . ':' . $requestName);
        $this->soapBody->appendChild($requestNode);

        $requestParamNode = $this->dom->createElement(
            self::REQUEST_NAMESPACE . ':' . $requestParamEncloserName
        );
        $requestNode->appendChild($requestParamNode);

        /* Add Request Parameters to Soap Body */
        foreach ($requestParameters as $key => $value) {
            $param = $this->dom->createElement(self::REQUEST_PARAM_NAMESPACE . ':' . $key, $value);
            $requestParamNode->appendChild($param);
        }

        return $this;
    }

    /**
     * Creates Nonce
     * @param string $created
     *
     * @return string
     */
    private function getNonce($created)
    {
        $random = random_int(0, PHP_INT_MAX);
        return base64_encode(
            pack('H*', sha1(pack('H*', $random) . pack('a*', $created) . pack('a*', $this->apiPassword)))
        );
    }
}
