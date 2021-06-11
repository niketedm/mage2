<?php

namespace Synchrony\DigitalBuy\Model\Sales\Order\Payment;

use Magento\Payment\Model\InfoInterface;

class AdditionalInfoManager
{
    const DIGITALBUY_TOKEN_KEY = 'synchrony_digitalbuy_token';
    const PROMO_AMOUNTS_KEY = 'synchrony_promo_amounts';
    const DEFAULT_PROMO_CODE_KEY = 'synchrony_default_promo_code';
    const RULE_METADATA_KEY = 'synchrony_rule_metadata';
    const ITEM_PROMO_AMOUNTS_KEY = 'synchrony_item_promo_amounts';
    const QUOTE_ITEM_PROMO_AMOUNTS_KEY = 'synchrony_quote_item_promo_amounts';
    const PAID_PROMO_AMOUNTS_KEY = 'synchrony_paid_promo_amounts';
    const REFUNDED_PROMO_AMOUNTS_KEY = 'synchrony_refunded_promo_amounts';

    /**
     * @var InfoInterface
     */
    private $payment;

    /**
     * @param InfoInterface $payment
     * @return $this
     */
    public function setPayment($payment)
    {
        $this->payment = $payment;
        return $this;
    }

    /**
     * Set digital buy token
     *
     * @param string $token
     * @return $this
     */
    public function setDigitalBuyToken($token)
    {
        $this->payment->setAdditionalInformation(self::DIGITALBUY_TOKEN_KEY, $token);
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDigitalBuyToken()
    {
        return $this->payment->getAdditionalInformation(self::DIGITALBUY_TOKEN_KEY);
    }

    /**
     * Set applied synchrony promotion amounts
     *
     * @param array $promoAmounts
     * @return $this
     */
    public function setPromoAmounts($promoAmounts)
    {
        $this->payment->setAdditionalInformation(self::PROMO_AMOUNTS_KEY, $promoAmounts);
        return $this;
    }

    /**
     * Retrieve applied synchrony promotion amounts
     *
     * @return array
     */
    public function getPromoAmounts()
    {
        return $this->payment->getAdditionalInformation(self::PROMO_AMOUNTS_KEY);
    }

    /**
     * Set applied default synchrony promo code
     *
     * @param string $promoCode
     * @return $this
     */
    public function setDefaultPromoCode($promoCode)
    {
        $this->payment->setAdditionalInformation(self::DEFAULT_PROMO_CODE_KEY, $promoCode);
        return $this;
    }

    /**
     * Retrieve applied default synchrony promo code
     *
     * @return array
     */
    public function getDefaultPromoCode()
    {
        return $this->payment->getAdditionalInformation(self::DEFAULT_PROMO_CODE_KEY);
    }

    /**
     * Set applied synchrony promo rules metadata
     *
     * @param string $promoCode
     * @return $this
     */
    public function setRuleMetadata($promoCode)
    {
        $this->payment->setAdditionalInformation(self::RULE_METADATA_KEY, $promoCode);
        return $this;
    }

    /**
     * Retrieve applied synchrony promo rules metadata
     *
     * @return array
     */
    public function getRuleMetadata()
    {
        return $this->payment->getAdditionalInformation(self::RULE_METADATA_KEY);
    }

    /**
     * Set applied synchrony promotion amounts per quote item id
     *
     * @param array $promoData
     * @return $this
     */
    public function setQuoteItemPromoAmounts($promoData)
    {
        $this->payment->setAdditionalInformation(self::QUOTE_ITEM_PROMO_AMOUNTS_KEY, $promoData);
        return $this;
    }

    /**
     * Retrieve applied synchrony promotion amounts per quote item id
     *
     * @return array
     */
    public function getQuoteItemPromoAmounts()
    {
        return $this->payment->getAdditionalInformation(self::QUOTE_ITEM_PROMO_AMOUNTS_KEY);
    }

    /**
     * Unset applied synchrony promotion amounts per quote item id
     *
     * @return $this
     */
    public function unsQuoteItemPromoAmounts()
    {
        $this->payment->unsAdditionalInformation(self::QUOTE_ITEM_PROMO_AMOUNTS_KEY);
        return $this;
    }

    /**
     * Set applied synchrony promotion amounts per order item id
     *
     * @param array $promoData
     * @return $this
     */
    public function setItemPromoAmounts($promoData)
    {
        $this->payment->setAdditionalInformation(self::ITEM_PROMO_AMOUNTS_KEY, $promoData);
        return $this;
    }

    /**
     * Retrieve applied synchrony promotion amounts per order item id
     *
     * @return array
     */
    public function getItemPromoAmounts()
    {
        return $this->payment->getAdditionalInformation(self::ITEM_PROMO_AMOUNTS_KEY);
    }

    /**
     * Set subtransactions data to payment transaction additional info
     *
     * @param array $subTransactionsData
     * @return $this
     */
    public function setTransactionSubTransactionsData($subTransactionsData)
    {
        $this->payment->setTransactionAdditionalInfo(
            Transaction\AdditionalInfoManager::SUB_TRANS_DATA_KEY,
            $subTransactionsData
        );
        return $this;
    }

    /**
     * Retrieve subtransactions data from payment transaction additional info
     *
     * @return null|array
     */
    public function getTransactionSubTransactionsData()
    {
        $result = null;
        $transactionAdditionalInfo = $this->payment->getTransactionAdditionalInfo();
        if (isset($transactionAdditionalInfo[Transaction\AdditionalInfoManager::SUB_TRANS_DATA_KEY])) {
            $result = $transactionAdditionalInfo[Transaction\AdditionalInfoManager::SUB_TRANS_DATA_KEY];
        }

        return $result;
    }

    /**
     * Add subtransaction data to payment transaction additional info
     *
     * @param string $key
     * @param array $transactionData
     * @return $this
     */
    public function addTransactionSubTransactionData($key, $transactionData)
    {
        $result = [];
        $subTransactionsData = $this->getTransactionSubTransactionsData();
        if ($subTransactionsData && is_array($subTransactionsData)) {
            $result = $subTransactionsData;
        }

        $result[$key] = $transactionData;
        $this->setTransactionSubTransactionsData($result);

        return $this;
    }

    /**
     * Set transaction raw details (API response)
     *
     * @return $this
     */
    public function setTransactionRawDetails($details)
    {
        $this->payment->setTransactionAdditionalInfo(Transaction::RAW_DETAILS, $details);
        return $this;
    }

    /**
     * Set transaction raw details (API response)
     *
     * @return array|null
     */
    public function getTransactionRawDetails()
    {
        return $this->payment->getTransactionAdditionalInfo(Transaction::RAW_DETAILS);
    }

    /**
     * Set paid promo amounts
     *
     * @param array $promoAmounts
     * @return $this
     */
    public function setPaidPromoAmounts($promoAmounts)
    {
        $this->payment->setAdditionalInformation(self::PAID_PROMO_AMOUNTS_KEY, $promoAmounts);
        return $this;
    }

    /**
     * Retrieve paid promo amounts
     *
     * @return array
     */
    public function getPaidPromoAmounts()
    {
        return $this->payment->getAdditionalInformation(self::PAID_PROMO_AMOUNTS_KEY);
    }

    /**
     * Add promo amount to paid list
     *
     * @param $promoCode
     * @param $amount
     * @return $this
     */
    public function addPaidPromoAmount($promoCode, $amount)
    {
        $paidPromoAmounts = $this->getPaidPromoAmounts() ?: [];
        if (!isset($paidPromoAmounts[$promoCode])) {
            $paidPromoAmounts[$promoCode] = 0;
        }
        $paidPromoAmounts[$promoCode] += $amount;
        $this->setPaidPromoAmounts($paidPromoAmounts);

        return $this;
    }

    /**
     * Set refunded promo amounts
     *
     * @param array $promoAmounts
     * @return $this
     */
    public function setRefundedPromoAmounts($promoAmounts)
    {
        $this->payment->setAdditionalInformation(self::REFUNDED_PROMO_AMOUNTS_KEY, $promoAmounts);
        return $this;
    }

    /**
     * Retrieve refunded promo amounts
     *
     * @return array
     */
    public function getRefundedPromoAmounts()
    {
        return $this->payment->getAdditionalInformation(self::REFUNDED_PROMO_AMOUNTS_KEY);
    }

    /**
     * Add promo amount to paid list
     *
     * @param $promoCode
     * @param $amount
     * @return $this
     */
    public function addRefundedPromoAmount($promoCode, $amount)
    {
        $paidPromoAmounts = $this->getRefundedPromoAmounts() ?: [];
        if (!isset($paidPromoAmounts[$promoCode])) {
            $paidPromoAmounts[$promoCode] = 0;
        }
        $paidPromoAmounts[$promoCode] += $amount;
        $this->setRefundedPromoAmounts($paidPromoAmounts);

        return $this;
    }
}
