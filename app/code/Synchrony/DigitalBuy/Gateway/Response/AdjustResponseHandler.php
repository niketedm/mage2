<?php

namespace Synchrony\DigitalBuy\Gateway\Response;

use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Payment\Gateway\Response\HandlerInterface;
use Magento\Sales\Api\TransactionRepositoryInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction\AdditionalInfoManager as TransAdditionalInfoManager;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Api\FilterBuilder;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\Transaction;

/**
 * Adjust response Handler
 */
class AdjustResponseHandler implements HandlerInterface
{
    /**
     * @var BuyServiceResponseReader
     */
    private $reader;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var TransactionRepositoryInterface
     */
    private $transactionRepository;

    /**
     * @var FilterBuilder
     */
    private $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    private $searchCriteriaBuilder;

    /**
     * @var TransAdditionalInfoManager
     */
    private $transAdditionalInfoManager;

    /**
     * @param BuyServiceResponseReader $reader
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param TransactionRepositoryInterface $transactionRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param TransAdditionalInfoManager $transAdditionalInfoManager
     */
    public function __construct(
        BuyServiceResponseReader $reader,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        TransactionRepositoryInterface $transactionRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        TransAdditionalInfoManager $transAdditionalInfoManager
    ) {
        $this->reader = $reader;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->transactionRepository = $transactionRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->transAdditionalInfoManager = $transAdditionalInfoManager;
    }

    /**
     * @inheritdoc
     */
    public function handle(array $handlingSubject, array $response)
    {
        $this->reader->setData($response);

        $paymentDO = SubjectReader::readPayment($handlingSubject);
        $payment = $paymentDO->getPayment();
        $order = $payment->getOrder();
        $promoCode = SubjectReader::readPromoCode($handlingSubject);
        $transactionId = $this->reader->getTransactionId();
        $parentTransactionIdPart = $promoCode . '-' . $transactionId;
        $paymentTransactionId = $payment->getTransactionId()
            ? $payment->getTransactionId() . '_' . $parentTransactionIdPart : $parentTransactionIdPart;
        $payment->setTransactionId($paymentTransactionId);

        $this->paymentAdditionalInfoManager->setPayment($payment)
            ->addRefundedPromoAmount($promoCode, SubjectReader::readAmount($handlingSubject))
            ->addTransactionSubTransactionData($promoCode, $response);

        $order->addStatusHistoryComment(__(
            'Executed Synchrony Adjust (Refund) for promo code %1, amount: %2. Transaction ID: "%3"',
            $promoCode,
            $order->getBaseCurrency()->formatTxt(SubjectReader::readAmount($handlingSubject)),
            $transactionId
        ));

        $this->closeRelatedForcePurchaseTransaction($payment, $promoCode);
    }

    /**
     * Close related force purchase transaction
     *
     * @param $payment
     * @param $promoCode
     * @return $this
     */
    private function closeRelatedForcePurchaseTransaction($payment, $promoCode)
    {
        $typeFilter = $this->filterBuilder
            ->setField(Transaction::TXN_TYPE)
            ->setValue(Transaction::TYPE_SYF_FORCE_PURCHASE)
            ->create();
        $idFilter = $this->filterBuilder
            ->setField(Transaction::PAYMENT_ID)
            ->setValue($payment->getId())
            ->create();
        $forcePurchaseTransactions = $this->transactionRepository->getList(
            $this->searchCriteriaBuilder
                ->addFilters([$typeFilter])
                ->addFilters([$idFilter])
                ->create()
        )->getItems();

        foreach ($forcePurchaseTransactions as $transaction) {
            if ($this->transAdditionalInfoManager->setTransaction($transaction)->getPromoCode() == $promoCode) {
                $transaction->setIsClosed(1);
                $payment->getOrder()->addRelatedObject($transaction);
                break;
            }
        }

        return $this;
    }
}
