<?php

namespace Synchrony\DigitalBuy\Model\Sales\Order\Payment;

class Transaction extends \Magento\Sales\Model\Order\Payment\Transaction
{
    const TYPE_SYF_FORCE_PURCHASE = 'syf_force_pur';
    const TYPE_SYF_ADJUST = 'syf_adjust';

    /**
     * @inheritdoc
     */
    public function setTxnType($txnType)
    {
        return $this->setData('txn_type', $txnType);
    }
}
