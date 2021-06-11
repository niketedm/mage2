<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Revolving;

use Magento\Payment\Gateway\CommandInterface;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;
use Synchrony\DigitalBuy\Gateway\Command\Revolving\ForcePurchaseCommandExeption;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Class CaptureCommand
 * @package Synchrony\DigitalBuy\Gateway\Command
 */
class CaptureCommand implements CommandInterface
{
    /**
     * @var BuyServiceResponseReader
     */
    private $forcePurchaseResponseReader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * Constructor
     *
     * @param CommandPoolInterface $commandPool
     * @param BuyServiceResponseReader $forcePurchaseResponseReader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        CommandPoolInterface $commandPool,
        BuyServiceResponseReader $forcePurchaseResponseReader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->commandPool = $commandPool;
        $this->forcePurchaseResponseReader = $forcePurchaseResponseReader;
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
        $this->paymentAdditionalInfoManager->setPayment($payment);
        $order = $payment->getOrder();
        $amount = SubjectReader::readAmount($commandSubject);
        $promoCodeAmounts = $this->paymentAdditionalInfoManager->getPromoAmounts();
        $promoCodesCount = count($promoCodeAmounts);

        if ($promoCodesCount > 1 && abs($amount - $order->getBaseGrandTotal()) > 0.0001) {
            throw new \Exception('Partial capture is not allowed in case of multiple promotions');
        }

        $payment->setTransactionId(null);
        $payment->resetTransactionAdditionalInfo();
        $payment->setIsTransactionClosed(false);

        if ($promoCodesCount == 1) {
            reset($promoCodeAmounts);
            $promoCodeAmounts[key($promoCodeAmounts)] = SubjectReader::readAmount($commandSubject);
        }

        try {
            foreach ($promoCodeAmounts as $code => $amount) {
                $commandSubject[SubjectReader::PROMO_CODE_KEY] = $code;
                $commandSubject[SubjectReader::AMOUNT_KEY] = $amount;
                $this->commandPool->get('force_purchase')->execute($commandSubject);
            }
        } catch (\Exception $e) {
            // handle partial capture state (surface transactions that came through)
            $forcePurchaseTransactions = $this->paymentAdditionalInfoManager->getTransactionSubTransactionsData();
            if ($forcePurchaseTransactions) {
                $transactionIds = [];
                foreach ($forcePurchaseTransactions as $txnInfo) {
                    $this->forcePurchaseResponseReader->setData($txnInfo);
                    $transactionIds[] = $this->forcePurchaseResponseReader->getTransactionId();
                }
                $messageSuffix = __(
                    '. Payment stuck in interim state. '
                        . 'Manual refund may be required for the following force purchase transactions: %1. '
                        . 'Please contact bank.',
                    implode(', ', $transactionIds)
                );
                if ($e instanceof ForcePurchaseCommandExeption) {
                    $newE = new ForcePurchaseCommandExeption($e->getMessage() . $messageSuffix, $e);
                    $newE->setFailCode($e->getFailCode());
                } else {
                    $newE = new \Exception($e->getMessage() . $messageSuffix, $e->getCode(), $e);
                }
                throw $newE;
            }
            throw $e;
        }
    }
}
