<?php
namespace Synchrony\DigitalBuy\Gateway\Response;

use Magento\Framework\DataObject;

class StatusInquiryReader extends DataObject
{
    /**
     * Response keys
     */
    const TRANSACTION_ID_KEY = 'transactionId';
    const TRANSACTION_DESCRIPTION_KEY = 'TransactionDescription';
    const TRANSACTION_DATE_KEY ='TransactionDate';
    const RESPONSE_CODE_KEY = 'responseCode';
    const RESPONSE_DESC_KEY = 'responseDesc';
    const TOKEN_ID_KEY = 'TokenId';
    const STATUS_CODE_KEY = 'StatusCode';
    const STATUS_MESSAGE_KEY = 'StatusMessage';
    const ACCOUNT_NUMBER_KEY = 'AccountNumber';
    const FIRST_NAME_KEY = 'FirstName';
    const LAST_NAME_KEY = 'LastName';
    const ADDRESS1_KEY = 'Address1';
    const ADDRESS2_KEY = 'Address2';
    const CITY_KEY = 'City';
    const STATE_KEY = 'State';
    const ZIP_CODE_KEY = 'ZipCode';
    const PROMO_CODE_KEY ='PromoCode';
    const AUTH_CODE_KEY = 'AuthCode';
    const ACCOUNT_TOKEN_KEY = 'AccountToken';
    const TRANSACTION_AMOUNT_KEY= 'TransactionAmount';
    const SETTLEMENT_ID_KEY= 'SettlementId';
    const CLIENT_TRANSACTION_ID_KEY = 'ClientTransactionID';
    const POSTBACK_ID_KEY = 'postbackid';

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
     * Check if transaction description is set in response
     *
     * @return bool
     */
    public function hasTransactionDescription()
    {
        return $this->hasData(self::TRANSACTION_DESCRIPTION_KEY);
    }

    /**
     * Retrieve transaction description from response
     *
     * @return string|null
     */
    public function getTransactionDescription()
    {
        return $this->getData(self::TRANSACTION_DESCRIPTION_KEY);
    }

    /**
     * Check if transaction date is set in response
     *
     * @return bool
     */
    public function hasTransactionDate()
    {
        return $this->hasData(self::TRANSACTION_DATE_KEY);
    }

    /**
     * Retrieve transaction date from response
     *
     * @return string|null
     */
    public function getTransactionDate()
    {
        return $this->getData(self::TRANSACTION_DATE_KEY);
    }

    /**
     * Check if synchrony response code is set in response
     *
     * @return bool
     */
    public function hasResponseCode()
    {
        return $this->hasData(self::RESPONSE_CODE_KEY);
    }

    /**
     * Retrieve synchrony response code from response
     *
     * @return string|null
     */
    public function getResponseCode()
    {
        return $this->getData(self::RESPONSE_CODE_KEY);
    }

    /**
     * Check if synchrony response description is set in response
     *
     * @return bool
     */
    public function hasResponseDesc()
    {
        return $this->hasData(self::RESPONSE_DESC_KEY);
    }

    /**
     * Retrieve synchrony response description from response
     *
     * @param bool $silent flag to either throw an exception or not if key not found in response
     * @return string|null
     */
    public function getResponseDesc()
    {
        return $this->getData(self::RESPONSE_DESC_KEY);
    }

    /**
     * Check if client token is set in response
     *
     * @return bool
     */
    public function hasTokenId()
    {
        return $this->hasData(self::TOKEN_ID_KEY);
    }

    /**
     * Retrieve client token from response
     *
     * @return string|null
     */
    public function getTokenId()
    {
        return $this->getData(self::TOKEN_ID_KEY);
    }

    /**
     * Check if status code is set in response
     *
     * @return bool
     */
    public function hasStatusCode()
    {
        return $this->hasData(self::STATUS_CODE_KEY);
    }

    /**
     * Retrieve status code from response
     *
     * @return string|null
     */
    public function getStatusCode()
    {
        return $this->getData(self::STATUS_CODE_KEY);
    }

    /**
     * Check if status message is set in response
     *
     * @return bool
     */
    public function hasStatusMessage()
    {
        return $this->hasData(self::STATUS_MESSAGE_KEY);
    }

    /**
     * Retrieve status message from response
     *
     * @return string|null
     */
    public function getStatusMessage()
    {
        return $this->getData(self::STATUS_MESSAGE_KEY);
    }

    /**
     * Check if account number is set in response
     *
     * @return bool
     */
    public function hasAccountNumber()
    {
        return $this->hasData(self::ACCOUNT_NUMBER_KEY);
    }

    /**
     * Retrieve account number from response
     *
     * @return string|null
     */
    public function getAccountNumber()
    {
        return $this->getData(self::ACCOUNT_NUMBER_KEY);
    }

    /**
     * Check if customer first name is set in response
     *
     * @return bool
     */
    public function hasFirstName()
    {
        return $this->hasData(self::FIRST_NAME_KEY);
    }

    /**
     * Retrieve customer first name from response
     *
     * @return string|null
     */
    public function getFirstName()
    {
        return $this->getData(self::FIRST_NAME_KEY);
    }

    /**
     * Check if customer last name is set in response
     *
     * @return bool
     */
    public function hasLastName()
    {
        return $this->hasData(self::LAST_NAME_KEY);
    }

    /**
     * Retrieve customer last name from response
     *
     * @return string|null
     */
    public function getLastName()
    {
        return $this->getData(self::LAST_NAME_KEY);
    }

    /**
     * Check if customer address line 1 is set in response
     *
     * @return bool
     */
    public function hasAddressLine1()
    {
        return $this->hasData(self::ADDRESS1_KEY);
    }

    /**
     * Retrieve customer address line 1 from response
     *
     * @return string|null
     */
    public function getAddressLine1()
    {
        return $this->getData(self::ADDRESS1_KEY);
    }

    /**
     * Check if customer address line 2 is set in response
     *
     * @return bool
     */
    public function hasAddressLine2()
    {
        return $this->hasData(self::ADDRESS2_KEY);
    }

    /**
     * Retrieve customer address line 2 from response
     *
     * @return string|null
     */
    public function getAddressLine2()
    {
        return $this->getData(self::ADDRESS2_KEY);
    }

    /**
     * Check if customer address city is set in response
     *
     * @return bool
     */
    public function hasAddressCity()
    {
        return $this->hasData(self::CITY_KEY);
    }

    /**
     * Retrieve customer address city from response
     *
     * @return string|null
     */
    public function getAddressCity()
    {
        return $this->getData(self::CITY_KEY);
    }

    /**
     * Check if customer address state is set in response
     *
     * @return bool
     */
    public function hasAddressState()
    {
        return $this->hasData(self::STATE_KEY);
    }

    /**
     * Retrieve customer address state from response
     *
     * @return string|null
     */
    public function getAddressState()
    {
        return $this->getData(self::STATE_KEY);
    }

    /**
     * Check if customer address zip is set in response
     *
     * @return bool
     */
    public function hasAddressZip()
    {
        return $this->hasData(self::ZIP_CODE_KEY)
            // workaround for status inquiry response for just approved accounts,
            // where zip code returned in different case
            || $this->hasData('Zipcode');
    }

    /**
     * Retrieve customer address zip from response
     *
     * @return string|null
     */
    public function getAddressZip()
    {
        $zip = $this->getData(self::ZIP_CODE_KEY);
        if ($zip === null) {
            // workaround for status inquiry response for just approved accounts,
            // where zip code returned in different case
            $zip = $this->getData('Zipcode');
        }
        return $zip;
    }

    /**
     * Check if promo code is set in response
     *
     * @return bool
     */
    public function hasPromoCode()
    {
        return $this->hasData(self::PROMO_CODE_KEY);
    }

    /**
     * Retrieve promo code from response
     *
     * @return string|null
     */
    public function getPromoCode()
    {
        return $this->getData(self::PROMO_CODE_KEY);
    }

    /**
     * Check if auth code is set in response
     *
     * @return bool
     */
    public function hasAuthCode()
    {
        return $this->hasData(self::AUTH_CODE_KEY);
    }

    /**
     * Retrieve auth code from response
     *
     * @return string|null
     */
    public function getAuthCode()
    {
        return $this->getData(self::AUTH_CODE_KEY);
    }

    /**
     * Check if account token is set in response
     *
     * @return bool
     */
    public function hasAccountToken()
    {
        return $this->hasData(self::ACCOUNT_TOKEN_KEY);
    }

    /**
     * Retrieve account token from response
     *
     * @return string|null
     */
    public function getAccountToken()
    {
        return $this->getData(self::ACCOUNT_TOKEN_KEY);
    }

    /**
     * Check if transaction amount is set in response
     *
     * @return bool
     */
    public function hasTransactionAmount()
    {
        return $this->hasData(self::TRANSACTION_AMOUNT_KEY);
    }

    /**
     * Retrieve transaction amount from response
     *
     * @return string|null
     */
    public function getTransactionAmount()
    {
        return $this->getData(self::TRANSACTION_AMOUNT_KEY);
    }

    /**
     * Check if settlement id is set in response
     *
     * @return bool
     */
    public function hasSettlementId()
    {
        return $this->hasData(self::PROMO_CODE_KEY);
    }

    /**
     * Retrieve settlement id from response
     *
     * @return string|null
     */
    public function getSettlementId()
    {
        return $this->getData(self::SETTLEMENT_ID_KEY);
    }

    /**
     * Check if client transaction id is set in response
     *
     * @return bool
     */
    public function hasClientTransactionId()
    {
        return $this->hasData(self::CLIENT_TRANSACTION_ID_KEY);
    }

    /**
     * Retrieve client transaction id from response
     *
     * @return string|null
     */
    public function getClientTransactionId()
    {
        return $this->getData(self::CLIENT_TRANSACTION_ID_KEY);
    }

    /**
     * Check if response contains postback id
     *
     * @return bool
     */
    public function hasPostbackId()
    {
        return $this->hasData(self::POSTBACK_ID_KEY);
    }

    /**
     * Retrieve postback id from response
     *
     * @return string|null
     */
    public function getPostbackId()
    {
        return $this->getData(self::POSTBACK_ID_KEY);
    }
}
