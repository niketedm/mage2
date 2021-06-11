<?php

namespace Synchrony\DigitalBuy\Gateway\Request;

use Magento\Payment\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Request\BuilderInterface;
use Synchrony\DigitalBuy\Gateway\Response\StatusInquiryReader;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Magento\Sales\Model\Order\Payment\Transaction;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager as TransAdditionalInfoManager;

/**
 * Class BuyServiceTokenBuilder
 */
class BuyServiceTokenBuilder implements BuilderInterface
{
    const TOKEN_KEY = 'Token';

    /**
     * @var TransAdditionalInfoManager
     */
    private $transAdditionalInfoManager;

    /**
     * @var StatusInquiryReader
     */
    private $statusInquiryReader;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * BuyServiceTokenBuilder constructor.
     * @param TransAdditionalInfoManager $transAdditionalInfoManager
     * @param StatusInquiryReader $statusInquiryReader
     * @param TransactionRepositoryInterface $transactionRepository
     */
    public function __construct(
        TransAdditionalInfoManager $transAdditionalInfoManager,
        StatusInquiryReader $statusInquiryReader,
        TransactionRepositoryInterface $transactionRepository
    ) {
        $this->transAdditionalInfoManager = $transAdditionalInfoManager;
        $this->statusInquiryReader = $statusInquiryReader;
        $this->transactionRepository = $transactionRepository;
    }

    /**
     * @inheritdoc
     */
    public function build(array $buildSubject)
    {
        $paymentDataObject = SubjectReader::readPayment($buildSubject);
        $payment = $paymentDataObject->getPayment();
        $authorizeTransaction = $payment->getAuthorizationTransaction();
        if ($authorizeTransaction->getTxnType() != Transaction::TYPE_AUTH) {
            // workaround for core behavior when it actually returns parent transaction and not really auth transaction
            $authorizeTransaction = $this->transactionRepository->getByTransactionType(
                Transaction::TYPE_AUTH,
                $payment->getId(),
                $payment->getOrder()->getId()
            );
        }
        $authorizeTransactionReader = $this->statusInquiryReader->setData(
            $this->transAdditionalInfoManager->setTransaction($authorizeTransaction)->getRawDetails()
        );
        return [
            self::TOKEN_KEY => $authorizeTransactionReader->getAccountToken()
        ];
    }
}
