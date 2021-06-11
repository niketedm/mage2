<?php

namespace Synchrony\DigitalBuy\Gateway\Helper;

use Magento\Framework\DataObject;

class SubjectReader extends \Magento\Payment\Gateway\Helper\SubjectReader
{
    const STORE_ID_KEY = 'store_id';
    const TOKEN_ID_KEY = 'token_id';
    const TOKEN_TIMESTAMP_KEY = 'token_timestamp';
    const SESSION_KEY = 'session';
    const QUOTE_KEY = 'quote';
    const UPDATED_ADDRESS_STORAGE_KEY = 'updated_address';
    const PAYMENT_KEY = 'payment';
    const PROMO_CODE_KEY = 'promo_code';
    const AMOUNT_KEY = 'amount';

    /**
     * Read store id from subject
     *
     * @param array $subject
     * @return int
     */
    public static function readStoreId(array $subject)
    {
        if (!isset($subject[self::STORE_ID_KEY])) {
            throw new \InvalidArgumentException('Store ID does not exist');
        }

        return $subject[self::STORE_ID_KEY];
    }

    /**
     * Read token from subject
     *
     * @param array $subject
     * @return string
     */
    public static function readToken(array $subject)
    {
        if (!isset($subject[self::TOKEN_ID_KEY])) {
            throw new \InvalidArgumentException('Token does not exist');
        }

        return $subject[self::TOKEN_ID_KEY];
    }

    /**
     * Read token timestamp from subject
     *
     * @param array $subject
     * @return int
     */
    public static function readTokenTimestamp(array $subject)
    {
        if (!isset($subject[self::TOKEN_TIMESTAMP_KEY])) {
            throw new \InvalidArgumentException('Token timestamp does not exist');
        }

        return $subject[self::TOKEN_TIMESTAMP_KEY];
    }

    /**
     * Read session from subject
     *
     * @param array $subject
     * @return \Synchrony\DigitalBuy\Model\Session
     */
    public static function readSession(array $subject)
    {
        if (!isset($subject[self::SESSION_KEY])) {
            throw new \InvalidArgumentException('Session does not exist');
        }

        return $subject[self::SESSION_KEY];
    }

    /**
     * Read quote from subject
     *
     * @param array $subject
     * @return \Magento\Quote\Model\Quote
     */
    public static function readQuote(array $subject)
    {
        if (!isset($subject[self::QUOTE_KEY])) {
            throw new \InvalidArgumentException('Quote does not exist');
        }

        return $subject[self::QUOTE_KEY];
    }

    /**
     * Read updated address storage object from subject
     *
     * @param array $subject
     * @return \Magento\Framework\DataObject
     */
    public static function readUpdatedAddress(array $subject)
    {
        if (!isset($subject[self::UPDATED_ADDRESS_STORAGE_KEY])) {
            throw new \InvalidArgumentException('Updated Address Storage does not exist');
        }

        return $subject[self::UPDATED_ADDRESS_STORAGE_KEY];
    }

    /**
     * Read promo code from subject
     *
     * @param array $subject
     * @return \Magento\Quote\Model\Quote
     */
    public static function readPromoCode(array $subject)
    {
        if (!isset($subject[self::PROMO_CODE_KEY])) {
            throw new \InvalidArgumentException('Promo Code does not exist');
        }

        return $subject[self::PROMO_CODE_KEY];
    }
}
