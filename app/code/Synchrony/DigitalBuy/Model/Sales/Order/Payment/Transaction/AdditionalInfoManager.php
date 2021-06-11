<?php

namespace Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction;

use Magento\Sales\Model\Order\Payment\Transaction;

class AdditionalInfoManager
{
    const SUB_TRANS_DATA_KEY = 'subtransacions';
    const PROMO_CODE_KEY = 'promo_code';

    /**
     * @var Transaction
     */
    private $transaction;

    /**
     * @param Transaction $payment
     * @return $this
     */
    public function setTransaction($transaction)
    {
        $this->transaction = $transaction;
        return $this;
    }

    /**
     * Set subtrasactions data
     *
     * @param $subTransactionsData
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function setSubTransactionsData($subTransactionsData)
    {
        $this->transaction->setAdditionalInformation(self::SUB_TRANS_DATA_KEY, $subTransactionsData);
        return $this;
    }

    /**
     * Get subtransactions data
     *
     * @return array|null
     */
    public function getSubTransactionsData()
    {
        return $this->transaction->getAdditionalInformation(self::SUB_TRANS_DATA_KEY);
    }

    /**
     * Unset subtransactions data
     *
     * @return $this
     */
    public function unsSubTransactionsData()
    {
        $this->transaction->unsAdditionalInformation(self::SUB_TRANS_DATA_KEY);
        return $this;
    }

    /**
     * Set promo code
     *
     * @param $promoCode
     * @return $this
     */
    public function setPromoCode($promoCode)
    {
        $this->transaction->setAdditionalInformation(self::PROMO_CODE_KEY, $promoCode);
        return $this;
    }

    /**
     * Get promo code
     *
     * @return string|null
     */
    public function getPromoCode()
    {
        return $this->transaction->getAdditionalInformation(self::PROMO_CODE_KEY);
    }

    /**
     * Set transaction raw details (API response)
     *
     * @return $this
     */
    public function setRawDetails($details)
    {
        $this->transaction->setAdditionalInformation(Transaction::RAW_DETAILS, $details);
        return $this;
    }

    /**
     * Set transaction raw details (API response)
     *
     * @return array|null
     */
    public function getRawDetails()
    {
        return $this->transaction->getAdditionalInformation(Transaction::RAW_DETAILS);
    }
}
