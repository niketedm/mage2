<?php

namespace Synchrony\DigitalBuy\Model;

use \Magento\Sales\Model\Order;
use Synchrony\DigitalBuy\Model\Sales\Order\Payment\AdditionalInfoManager as PaymentAdditionalInfoManager;

class RuleApplier
{
    /**
     * @var \Synchrony\DigitalBuy\Api\Data\RuleInterface[]
     */
    private $appliedRules = [];

    /**
     * @var \Magento\Framework\Math\Calculator
     */
    private $rounder;

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var string
     */
    private $defaultPromoCode;

    /**
     * @var \Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig
     */
    private $config;

    /**
     * @var \Magento\Framework\Math\CalculatorFactory
     */
    private $mathCalculatorFactory;

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule\Validator
     */
    private $validator;

    /**
     * @var PaymentAdditionalInfoManager
     */
    private $paymentAdditionalInfoManager;

    /**
     * @param \Magento\Store\Model\StoreManagerInterface $storeManager
     * @param \Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig $config
     * @param \Magento\Framework\Math\CalculatorFactory $mathCalculatorFactory,
     * @param \Synchrony\DigitalBuy\Model\Rule\Validator $validator
     * @param PaymentAdditionalInfoManager $paymentAdditionalInfoManager
     */
    public function __construct(
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Synchrony\DigitalBuy\Gateway\Config\RevolvingConfig $config,
        \Magento\Framework\Math\CalculatorFactory $mathCalculatorFactory,
        \Synchrony\DigitalBuy\Model\Rule\Validator $validator,
        PaymentAdditionalInfoManager $paymentAdditionalInfoManager
    ) {
        $this->storeManager = $storeManager;
        $this->config = $config;
        $this->mathCalculatorFactory = $mathCalculatorFactory;
        $this->validator = $validator;
        $this->paymentAdditionalInfoManager = $paymentAdditionalInfoManager;
    }

    /**
     * Process Promotion Rules applied on order and aggregates promo codes values applied
     *
     * @param Order $order
     * @return array
     */
    public function applyPromotions(Order $order)
    {
        $this->init($order);

        $itemAmounts = $this->calculateItemAmounts($order);

        /* Aggregate item amount to promo codes */
        $promoTotals = [];
        foreach ($itemAmounts as $item) {
            $promoTotals[$item['promo_code']] = isset($promoTotals[$item['promo_code']])
                ? $promoTotals[$item['promo_code']] + $item['amount'] : $item['amount'];
        }

        $this->paymentAdditionalInfoManager->setPayment($order->getPayment())
            ->setPromoAmounts($promoTotals)
            ->setQuoteItemPromoAmounts($itemAmounts)
            ->setDefaultPromoCode($this->defaultPromoCode)
            ->setRuleMetadata($this->getAppliedRulesMetadata());
    }

    /**
     * Initialize/reset calculator
     *
     * @param Order $order
     * @return $this
     */
    private function init(Order $order)
    {
        $this->defaultPromoCode = $this->config->getDefaultPromoCode($order->getStoreId());
        $this->rounder = $this->mathCalculatorFactory->create();
        $this->appliedRules = [];
        if (!$order->getId()) {
            foreach ($order->getAllItems() as $item) {
                $item->setOrder($order);
            }
        }
        return $this;
    }

    /**
     * Apply promotion rules and calculate promotion amounts per order item (quote item id based though, since
     * order item ids are not yet available)
     * returns 2 dimentional array of the following structure
     * [
     *     $quoteItemId => [
     *         'promo_code' => $promoCode,
     *         'amount' => $amount
     *     ]
     * ]
     *
     * @param \Magento\Sales\Model\Order $order
     * @return array
     */
    private function calculateItemAmounts(\Magento\Sales\Model\Order $order)
    {
        $websiteId = $this->storeManager->getStore($order->getStoreId())->getWebsiteId();
        $this->validator->init($websiteId, $order->getCustomerGroupId());
        $itemAmounts = [];
        $itemAmountsTotal = 0;
        foreach ($order->getAllItems() as $item) {
            if (($item->getChildrenItems() && $item->isChildrenCalculated())
                || ($item->hasParentItem() && !$item->getParentItem()->isChildrenCalculated())
                || $item->getBaseRowTotal() < 0.0001) {
                continue;
            }
            $promoRule = $this->validator->process($item);
            if ($promoRule !== false) {
                $promoCode = $promoRule->getCode();
                $this->appliedRules[$promoRule->getId()] = $promoRule;
            } else {
                $promoCode = $this->defaultPromoCode;
            }
            $itemAmounts[$item->getQuoteItemId()] = [
                'promo_code' => $promoCode,
                'amount' => $item->getBaseRowTotal()
            ];
            $itemAmountsTotal += $item->getBaseRowTotal();
        }

        $orderGrandTotal = $order->getBaseGrandTotal();
        $extraAmount = $orderGrandTotal - $itemAmountsTotal;
        if (abs($extraAmount) > 0.0001 && $itemAmountsTotal > 0.0001) {
            $ratio = $extraAmount / $itemAmountsTotal;
            foreach ($itemAmounts as &$item) {
                $item['amount'] += $this->rounder->deltaRound($item['amount'] * $ratio);
            }
        }

        return $itemAmounts;
    }

    /**
     * Get applied rules metadata
     *
     * @return array
     */
    private function getAppliedRulesMetadata()
    {
        $metadata = [];
        foreach ($this->appliedRules as $rule) {
            /** @var \Synchrony\DigitalBuy\Api\Data\RuleInterface $rule */
            $metadata[$rule->getId()] = [
                'promo_code' => $rule->getCode(),
                'name' => $rule->getName()
            ];
        }

        return $metadata;
    }
}
