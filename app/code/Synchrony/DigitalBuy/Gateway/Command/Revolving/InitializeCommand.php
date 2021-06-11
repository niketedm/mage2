<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Revolving;

use Magento\Payment\Gateway\CommandInterface;
use Magento\Payment\Gateway\Helper\ContextHelper;
use Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig;
use Synchrony\DigitalBuy\Gateway\Helper\SubjectReader;
use Magento\Sales\Model\Order;
use Magento\Sales\Api\Data\OrderInterface;
use Magento\Sales\Model\Order\Payment;
use Synchrony\DigitalBuy\Model\RuleApplier;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Class InitializeCommand
 * @package Synchrony\DigitalBuy\Gateway\Command
 */
class InitializeCommand implements CommandInterface
{
    /**
     * @var RuleApplier
     */
    private $ruleApplier;

    /**
     * @var Initialize\RuleValidator
     */
    private $ruleValidator;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * InitializeCommand constructor.
     * @param RuleApplier $ruleApplier
     * @param Initialize\RuleValidator $ruleValidator
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        RuleApplier $ruleApplier,
        Initialize\RuleValidator $ruleValidator,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->ruleApplier = $ruleApplier;
        $this->ruleValidator = $ruleValidator;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->logger = $logger;
    }

    /**
     * @param array $commandSubject
     */
    public function execute(array $commandSubject)
    {
        $stateObject = SubjectReader::readStateObject($commandSubject);
        $paymentDataObject = SubjectReader::readPayment($commandSubject);

        /** @var Payment $payment */
        $payment = $paymentDataObject->getPayment();
        if (!$payment instanceof Payment) {
            throw new \LogicException('Order Payment should be provided');
        }

        $this->paymentAdditionalInfoManager->setPayment($payment);

        $order = $payment->getOrder();
        $this->ruleApplier->applyPromotions($order);
        $this->logger->debug(
            'Promo codes for order id ' . $order->getIncrementId() . ': '
            . var_export($this->paymentAdditionalInfoManager->getPromoAmounts(), true)
        );

        $this->ruleValidator->validate($payment);

        $payment->getOrder()->setCanSendNewEmailFlag(false);

        $stateObject->setData(OrderInterface::STATE, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData(OrderInterface::STATUS, Order::STATE_PENDING_PAYMENT);
        $stateObject->setData('is_notified', false);
    }
}
