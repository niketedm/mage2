<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Revolving\Initialize;

use Magento\Framework\Exception\LocalizedException;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class RuleValidator
{
    /**
     * Maximum no of promotions allowed
     */
    const MAX_PROMOTIONS_ALLOWED = 3;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var RuleValidator\ErrorNotifier
     */
    private $errorNotifier;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * RuleValidator constructor.
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param RuleValidator\ErrorNotifier $errorNotifier
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        RuleValidator\ErrorNotifier $errorNotifier,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->errorNotifier = $errorNotifier;
        $this->logger = $logger;
    }

    /**
     * Validate promo data
     *
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @throws RuleValidator\Exception
     */
    public function validate($payment)
    {
        $promoData = $this->paymentAdditionalInfoManager->setPayment($payment)->getPromoAmounts();
        $promoCount = count($promoData);
        if ($promoCount == 0) {
            throw new LocalizedException(__('Synchrony payment cannot be used for zero subtotal carts'));
        }

        if ($promoCount > self::MAX_PROMOTIONS_ALLOWED) {
            $errorMsg = __("Promotions applied exceeded limit of %1", self::MAX_PROMOTIONS_ALLOWED);
            $this->errorNotifier->sendErrorNotification($errorMsg, $payment);
            $this->logger->critical($errorMsg);
            throw new LocalizedException(__(
                'Unfortunately your cart contains too many different financing promotions. '
                . 'You can try removing some items from the cart or contact us for assistance.'
            ));
        }
    }
}
