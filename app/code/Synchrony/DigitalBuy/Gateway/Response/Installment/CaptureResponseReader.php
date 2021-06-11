<?php
namespace Synchrony\DigitalBuy\Gateway\Response\Installment;

use Magento\Framework\DataObject;

class CaptureResponseReader extends DataObject
{
    /**
     * Response keys
     */
    const TRANSACTION_ID_KEY = 'transactionId';
    const RESPONSE_TEXT_KEY  = 'responseText';

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
     * Retrieve Response description from response
     *
     * @return string|null
     */
    public function getResponseText()
    {
        return $this->getData(self::RESPONSE_TEXT_KEY);
    }
}
