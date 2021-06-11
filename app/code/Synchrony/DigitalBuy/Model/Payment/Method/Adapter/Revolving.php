<?php

namespace Synchrony\DigitalBuy\Model\Payment\Method\Adapter;

use Magento\Framework\Event\ManagerInterface;
use Magento\Payment\Gateway\Command\CommandManagerInterface;
use Magento\Payment\Gateway\Command\CommandPoolInterface;
use Magento\Payment\Gateway\Config\ValueHandlerPoolInterface;
use Magento\Payment\Gateway\Data\PaymentDataObjectFactory;
use Magento\Payment\Gateway\Validator\ValidatorPoolInterface;
use Psr\Log\LoggerInterface;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;
use Magento\Quote\Api\Data\CartInterface;

class Revolving extends \Synchrony\DigitalBuy\Model\Payment\Method\AbstractAdapter
{
    /**
     * @var PaymentAdditionalInfoManager
     */
    protected $paymentAdditionalInfoManager;

    /**
     * @var string
     */
    private $lastCartValidationMsg;

    /**
     * Adapter constructor.
     *
     * @param ManagerInterface $eventManager
     * @param ValueHandlerPoolInterface $valueHandlerPool
     * @param PaymentDataObjectFactory $paymentDataObjectFactory
     * @param string $code
     * @param string $formBlockType
     * @param string $infoBlockType
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param CommandPoolInterface|null $commandPool
     * @param ValidatorPoolInterface|null $validatorPool
     * @param CommandManagerInterface|null $commandExecutor
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ManagerInterface $eventManager,
        ValueHandlerPoolInterface $valueHandlerPool,
        PaymentDataObjectFactory $paymentDataObjectFactory,
        string $code,
        string $formBlockType,
        string $infoBlockType,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        CommandPoolInterface $commandPool = null,
        ValidatorPoolInterface $validatorPool = null,
        CommandManagerInterface $commandExecutor = null,
        LoggerInterface $logger = null
    ) {
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        parent::__construct(
            $eventManager,
            $valueHandlerPool,
            $paymentDataObjectFactory,
            $code,
            $formBlockType,
            $infoBlockType,
            $commandPool,
            $validatorPool,
            $commandExecutor,
            $logger
        );
    }

    /**
     * @inheritdoc
     */
    public function isAvailable(CartInterface $quote = null)
    {
        $result = parent::isAvailable($quote);
        if (!$result) {
            return $result;
        }

        if ($quote && $quote->getBaseSubtotal() !== null && $quote->getBaseSubtotal() < 0.0001) {
            return false;
        }

        return $result;
    }

    /**
     * @inheritdoc
     */
    public function canCapturePartial()
    {
        return parent::canCapturePartial() && $this->isSinglePromotionApplied();
    }

    /**
     * Check if single synchrony promotion is applied to order
     * returns false in case when not able to retrieve promo data as well
     *
     * @return bool
     */
    private function isSinglePromotionApplied()
    {
        $infoInstance = $this->getInfoInstance();
        if (!$infoInstance) {
            return false;
        }

        $promotionData = $this->paymentAdditionalInfoManager->setPayment($infoInstance)
            ->getPromoAmounts();
        if ($promotionData && count($promotionData) == 1) {
            return true;
        }

        return false;
    }
}
