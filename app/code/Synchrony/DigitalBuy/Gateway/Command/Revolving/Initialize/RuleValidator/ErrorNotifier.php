<?php

namespace Synchrony\DigitalBuy\Gateway\Command\Revolving\Initialize\RuleValidator;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Psr\Log\LoggerInterface;
use Magento\Sales\Model\Order;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

/**
 * Handles Error Notification service
 */
class ErrorNotifier
{
    /**
     * ERROR NOTIFICATION EMAIL TEMPLATE ID
     */
    const NOTIFICATION_TEMPLATE_ID = 'synchrony_digitalbuy_error_notification_template';

    /**
     * Store config
     *
     * @var ScopeConfigInterface
     */
    private $scopeConfig;

    /**
     * @var StateInterface
     */
    private $inlineTranslation;

    /**
     * @var TransportBuilder
     */
    private $transportBuilder;

    /**
     * @var TimezoneInterface
     */
    private $localeDate;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Payment\Model\InfoInterface
     */
    private $payment;

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param TimezoneInterface $localeDate
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     * @param LoggerInterface $logger
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        TimezoneInterface $localeDate,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager,
        LoggerInterface $logger
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->localeDate = $localeDate;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
        $this->logger = $logger;
    }

    /**
     * Sends an email about failed transaction.
     *
     * @param string $errorMessage
     * @param \Magento\Payment\Model\InfoInterface $payment
     * @return $this
     */
    public function sendErrorNotification($errorMessage, $payment)
    {
        $this->payment = $payment;
        $this->paymentAdditionalInfoManager->setPayment($payment);
        $order = $this->payment->getOrder();

        $this->inlineTranslation->suspend();

        $receiver = $this->getConfigValue('checkout/payment_failed/receiver', $order);
        $sendTo = [
            [
                'email' => $this->getConfigValue('trans_email/ident_' . $receiver . '/email', $order),
                'name' => $this->getConfigValue('trans_email/ident_' . $receiver . '/name', $order),
            ],
        ];

        $copyMethod = $this->getConfigValue('checkout/payment_failed/copy_method', $order);
        $copyTo = $this->getCopyToEmails($order);

        $bcc = [];
        if (!empty($copyTo)) {
            switch ($copyMethod) {
                case 'bcc':
                    $bcc = $copyTo;
                    break;
                case 'copy':
                    foreach ($copyTo as $email) {
                        $sendTo[] = ['email' => $email, 'name' => null];
                    }
                    break;
            }
        }

        foreach ($sendTo as $recipient) {
            $transport = $this->transportBuilder
                ->setTemplateIdentifier(self::NOTIFICATION_TEMPLATE_ID)
                ->setTemplateOptions([
                    'area' => FrontNameResolver::AREA_CODE,
                    'store' => Store::DEFAULT_STORE_ID,
                ])
                ->setTemplateVars($this->getTemplateVars($errorMessage))
                ->setFrom($this->getConfigValue('checkout/payment_failed/identity', $order))
                ->addTo($recipient['email'], $recipient['name'])
                ->addBcc($bcc)
                ->getTransport();

            try {
                $transport->sendMessage();
            } catch (\Exception $e) {
                $this->logger->critical($e->getMessage());
            }
        }
        $this->inlineTranslation->resume();
        return $this;
    }

    /**
     * Returns mail template variables.
     *
     * @param string $errorMessage
     * @return array
     */
    private function getTemplateVars($errorMessage)
    {
        $order = $this->payment->getOrder();

        $nameOnbillingAddress = $order->getBillingAddress()->getFirstname()
            . ' ' . $order->getBillingAddress()->getLastname();
        return [
            'reason' => $errorMessage,
            'dateAndTime' => $this->getLocaleDate(),
            'nameOnBillingAddress' => $nameOnbillingAddress,
            'customerEmail' => $order->getCustomerEmail(),
            'defaultPromoCode' => $this->paymentAdditionalInfoManager->getDefaultPromoCode(),
            'appliedPromoRules' => $this->getAppliedPromoRules(),
            'items' => $this->getOrderItems()
        ];
    }

    /**
     * Returns scope config value by config path.
     *
     * @param string $configPath
     * @param Order $order
     * @return mixed
     */
    private function getConfigValue(string $configPath, Order $order)
    {
        return $this->scopeConfig->getValue(
            $configPath,
            ScopeInterface::SCOPE_STORE,
            $order->getStoreId()
        );
    }

    /**
     * Gets email values by configuration path.
     *
     * @param Order $order
     * @return array|false
     */
    private function getCopyToEmails(Order $order)
    {
        $configData = $this->getConfigValue('checkout/payment_failed/copy_to', $order);
        if (!empty($configData)) {
            return explode(',', $configData);
        }

        return false;
    }

    /**
     * Returns order visible items info.
     *
     * @return array
     */
    private function getOrderItems()
    {
        $order = $this->payment->getOrder();

        $itemSummary = [];
        $itemPromoData = $this->paymentAdditionalInfoManager->getQuoteItemPromoAmounts();
        foreach ($order->getAllVisibleItems() as $item) {
            if (!$item->getId() && $item->hasParentItem()) {
                // double checking for parent item when items do not have ids yet
                // getAllVisibleItems checks by existane of parentItemId
                continue;
            }
            $itemSummary[] = [
                'name' => $item->getProduct()->getName(),
                'sku' => $item->getProduct()->getSku(),
                'promo_code' => $itemPromoData[$item->getQuoteItemId()]['promo_code']
            ];
        }
        return $itemSummary;
    }

    /**
     * Get applied promo rules as a string
     *
     * @return string
     */
    private function getAppliedPromoRules()
    {
        $ruleMetadata = $this->paymentAdditionalInfoManager->getRuleMetadata();
        $promoRuleStrings = [];
        foreach ($ruleMetadata as $rule) {
            $promoRuleStrings[] = htmlentities($rule['name'] . ' (' . $rule['promo_code'] . ')');
        }

        return implode('<br />', $promoRuleStrings);
    }

    /**
     * Returns current locale date and time
     *
     * @return string
     */
    private function getLocaleDate(): string
    {
        return $this->localeDate->formatDateTime(
            new \DateTime(),
            \IntlDateFormatter::MEDIUM,
            \IntlDateFormatter::MEDIUM
        );
    }
}
