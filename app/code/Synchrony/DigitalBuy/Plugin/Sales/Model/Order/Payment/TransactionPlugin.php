<?php

namespace Synchrony\DigitalBuy\Plugin\Sales\Model\Order\Payment;

use Synchrony\DigitalBuy\Gateway\Config\InstallmentConfig;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Synchrony\DigitalBuy\Gateway\Response\BuyServiceResponseReader;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\TransactionFactory;
use Magento\Sales\Api\Data\TransactionInterface;

class TransactionPlugin
{
    /**
     * @var array
     */
    private $subTransactions;

    /**
     * @var string
     */
    private $txnType;

    /**
     * @var TransactionFactory
     */
    private $transactionFactory;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var BuyServiceResponseReader
     */
    private $buyServiceResponseReader;

    /**
     * @var AdditionalInfoManager
     */
    private $additionalInfoManager;

    /**
     * @var array
     */
    private $parentToSubMap = [
        TransactionInterface::TYPE_CAPTURE => Transaction::TYPE_SYF_FORCE_PURCHASE,
        TransactionInterface::TYPE_REFUND => Transaction::TYPE_SYF_ADJUST
    ];

    /**
     * TransactionPlugin constructor.
     * @param TransactionFactory $transactionFactory
     * @param TransactionRepositoryInterface $transactionRepository
     * @param BuyServiceResponseReader $buyServiceResponseReader
     * @param AdditionalInfoManager $additionalInfoManager
     */
    public function __construct(
        TransactionFactory $transactionFactory,
        TransactionRepositoryInterface $transactionRepository,
        BuyServiceResponseReader $buyServiceResponseReader,
        AdditionalInfoManager $additionalInfoManager
    ) {
        $this->transactionFactory = $transactionFactory;
        $this->transactionRepository = $transactionRepository;
        $this->buyServiceResponseReader = $buyServiceResponseReader;
        $this->additionalInfoManager = $additionalInfoManager;
    }

    /**
     * Check if transaction has data about child transactions in additional data,
     * if so extract it to save an standalone transactions
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $subject
     */
    public function beforeSave(\Magento\Sales\Model\Order\Payment\Transaction $subject)
    {
        $methodCode = $subject->getOrder()->getPayment()->getMethod();
        if (!in_array($methodCode, [RevolvingConfig::METHOD_CODE, InstallmentConfig::METHOD_CODE])
            || !in_array($subject->getTxnType(), array_keys($this->parentToSubMap))) {
            return;
        }

        $subTransactions = $this->additionalInfoManager->setTransaction($subject)->getSubTransactionsData();
        if ($subTransactions != null) {
            $this->subTransactions = $subTransactions;
            $this->additionalInfoManager->unsSubTransactionsData();
        }
    }

    /**
     * Save children as standalone transactions
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $subject
     * @param $result
     * @return mixed
     */
    public function afterSave(\Magento\Sales\Model\Order\Payment\Transaction $subject, $result)
    {
        $methodCode = $subject->getOrder()->getPayment()->getMethod();
        if (!in_array($methodCode, [RevolvingConfig::METHOD_CODE, InstallmentConfig::METHOD_CODE])
            || !$this->subTransactions) {
            return $result;
        }

        foreach ($this->subTransactions as $promoCode => $txnInfo) {
            $this->buyServiceResponseReader->setData($txnInfo);
            $transaction = $this->transactionFactory->create()
                ->setTxnId($this->buyServiceResponseReader->getTransactionId())
                ->setParentId($subject->getId())
                ->setParentTxnId($subject->getTxnId())
                ->setPaymentId($subject->getPaymentId())
                ->setOrderId($subject->getOrderId())
                ->setTxnType($this->parentToSubMap[$subject->getTxnType()])
                ->setIsClosed((int)($subject->getTxnType() != TransactionInterface::TYPE_CAPTURE))
                ->isFailsafe(true);
            $this->additionalInfoManager->setTransaction($transaction)
                ->setPromoCode($promoCode)
                ->setRawDetails($txnInfo);
            $this->transactionRepository->save($transaction);
        }

        $this->subTransactions = null;

        return $result;
    }

    /**
     * Extend transaction types list with Synchrony types
     *
     * @param \Magento\Sales\Model\Order\Payment\Transaction $subject
     * @param $result
     * @return array
     */
    public function afterGetTransactionTypes(\Magento\Sales\Model\Order\Payment\Transaction $subject, $result)
    {
        $result[Transaction::TYPE_SYF_FORCE_PURCHASE] = __('ForcePurchase (Synchrony)');
        $result[Transaction::TYPE_SYF_ADJUST] = __('Adjust/Refund (Synchrony)');
        return $result;
    }
}
