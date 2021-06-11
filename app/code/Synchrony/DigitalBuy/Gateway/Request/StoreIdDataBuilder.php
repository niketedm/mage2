<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;

class StoreIdDataBuilder implements BuilderInterface
{
    const STORE_ID_KEY = 'store_id';

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $result = [];
        $storeId = null;
        try {
            $storeId = SubjectReader::readStoreId($buildSubject);
        } catch (\Exception $e) {}

        // fallback to order
        if ($storeId === null) {
            try {
                $payment = SubjectReader::readPayment($buildSubject)->getPayment();
                if (is_object($payment) && is_object($payment->getOrder())) {
                    $storeId = $payment->getOrder()->getStoreId();
                }
            } catch (\Exception $e) {}
        }

        // fallback to quote
        if ($storeId === null) {
            try {
                $storeId = SubjectReader::readQuote($buildSubject)->getStoreId();
            } catch (\Exception $e) {}
        }

        $result[self::STORE_ID_KEY] = $storeId;
        return $result;
    }
}
