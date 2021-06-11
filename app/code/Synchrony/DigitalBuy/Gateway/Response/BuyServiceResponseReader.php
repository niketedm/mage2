<?php
namespace Synchrony\DigitalBuy\Gateway\Response;

use Magento\Framework\DataObject;

class BuyServiceResponseReader extends DataObject
{
    /**
     * Response keys
     */
    const TRANSACTION_ID_KEY = 'TransactionId';
    const RESPONSE_CODE_KEY  = 'ResponseCode';
    const RESPONSE_TEXT_KEY  = 'ResponseText';
    const STATUS_KEY = 'Status';
    const FAULT_CODE_KEY ='faultcode';
    const FAULT_STRING_KEY ='faultstring';
    const LIST_OF_ERRORS_KEY ='ListOfErrors';

    /**
     * Check if transaction id is set in response
     *
     * @return bool
     */
    public function hasTransactionId()
    {
        return $this->hasData(self::TRANSACTION_ID_KEY);
    }

    /**
     * Retrieve transaction id from response
     *
     * @return string|null
     */
    public function getTransactionId()
    {
        return $this->getData(self::TRANSACTION_ID_KEY);
    }

    /**
     * Check if  Response code is set in response
     *
     * @return bool
     */
    public function hasResponseCode()
    {
        return $this->hasData(self::RESPONSE_CODE_KEY);
    }

    /**
     * Retrieve Response code from response
     *
     * @return string|null
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE_KEY);
    }

    /**
     * Check if Response description is set in response
     *
     * @return bool
     */
    public function hasResponseText()
    {
        return $this->hasData(self::RESPONSE_TEXT_KEY);
    }

    /**
     * Retrieve Response description from response
     *
     * @return string|null
     */
    public function getResponseText()
    {
        return $this->getData(self::RESPONSE_TEXT_KEY);
    }

    /**
     * Check if Status mesasage is set in response
     *
     * @return bool
     */
    public function hasStatus()
    {
        return $this->hasData(self::STATUS_KEY);
    }

    /**
     * Retrieve Status message from response
     *
     * @return string|null
     */
    public function getStatus()
    {
        return $this->getData(self::STATUS_KEY);
    }

    /**
     * Check if Fault code is set in response
     *
     * @return bool
     */
    public function hasFaultCode()
    {
        return $this->hasData(self::FAULT_CODE_KEY);
    }

    /**
     * Retrieve Fault code from response
     *
     * @return string|null
     */
    public function getFaultCode()
    {
        return $this->getData(self::FAULT_CODE_KEY);
    }

    /**
     * Check if Fault string is set in response
     *
     * @return bool
     */
    public function hasFaultString()
    {
        return $this->hasData(self::FAULT_STRING_KEY);
    }

    /**
     * Retrieve Fault string from response
     *
     * @return string|null
     */
    public function getFaultString()
    {
        return $this->getData(self::FAULT_STRING_KEY);
    }

    /**
     * Check if Error List is set in response
     *
     * @return bool
     */
    public function hasErrors()
    {
        return $this->hasData(self::LIST_OF_ERRORS_KEY);
    }

    /**
     * Retrieve Error from response
     *
     * @return string|null
     */
    public function getErrors()
    {
        return $this->getData(self::LIST_OF_ERRORS_KEY);
    }
}
