<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\CommandInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Sales\Api\Data\TransactionInterface;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Class RefundCommand
 * @package Synchrony\DigitalBuy\Gateway\Command
 */
class RefundCommand implements CommandInterface
{
    /**
     * @var Refund\RefundCalculator
     */
    private $refundCalculator;

    /**
     * @var CommandPoolInterface
     */
    private $commandPool;

    /**
     * @var BuyServiceResponseReader
     */
    private $adjustResponseReader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * Constructor
     *
     * @param Refund\RefundCalculator $refundCalculator
     * @param CommandPoolInterface $commandPool
     * @param BuyServiceResponseReader $adjustResponseReader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        Refund\RefundCalculator $refundCalculator,
        CommandPoolInterface $commandPool,
        BuyServiceResponseReader $adjustResponseReader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->refundCalculator = $refundCalculator;
        $this->commandPool = $commandPool;
        $this->adjustResponseReader = $adjustResponseReader;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
    }

    /**
     * @param array $commandSubject
     * @return void
     */
    public function execute(array $commandSubject)
    {
        $paymentDO = SubjectReader::readPayment($commandSubject);
        $payment = $paymentDO->getPayment();
        $refundPromoCodes = $this->refundCalculator->collectRefundPromoCodes($payment);

        $payment->setTransactionId(null);

        try {
            foreach ($refundPromoCodes as $promoCode => $amount) {
                $commandSubject[SubjectReader::PROMO_CODE_KEY] = $promoCode;
                $commandSubject[SubjectReader::AMOUNT_KEY] = $amount;
                $this->commandPool->get('adjust')->execute($commandSubject);
            }
        } catch (\Exception $e) {
            // handle partial/incomplete refund state (surface transactions that came through)
            $subTransactions = $this->paymentAdditionalInfoManager->getTransactionSubTransactionsData();
            if ($subTransactions) {
                $transactionIds = [];
                foreach ($subTransactions as $txnInfo) {
                    $this->adjustResponseReader->setData($txnInfo);
                    $transactionIds[] = $this->adjustResponseReader->getTransactionId();
                }
                $messageSuffix = __(
                    '. Refund stuck in interim state. Failed to execure/process ALL refund API calls. '
                    . 'Following refund transactions came through: %1. '
                    . 'However information about them has not been saved as part of the order. '
                    . 'Further manual processing may be required.',
                    implode(', ', $transactionIds)
                );

                throw new \Exception($e->getMessage() . $messageSuffix, $e->getCode(), $e);
            }
            throw $e;
        }
    }
}
