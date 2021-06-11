<?php

namespace Synchrony\DigitalBuy\Model\Rule\Condition;

/**
 * @api
 */
class Combine extends \Magento\Rule\Model\Condition\Combine
{
    /**
     * Core event manager proxy
     *
     * @var \Magento\Framework\Event\ManagerInterface
     */
    protected $_eventManager = null;

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule\Condition\Order
     */
    protected $_conditionOrder;

    /**
     * @param \Magento\Rule\Model\Condition\Context $context
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Synchrony\DigitalBuy\Model\Rule\Condition\Order $conditionOrder
     * @param array $data
     */
    public function __construct(
        \Magento\Rule\Model\Condition\Context $context,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Synchrony\DigitalBuy\Model\Rule\Condition\Order $conditionOrder,
        array $data = []
    ) {
        $this->_eventManager = $eventManager;
        $this->_conditionOrder = $conditionOrder;
        parent::__construct($context, $data);
        $this->setType(\Synchrony\DigitalBuy\Model\Rule\Condition\Combine::class);
    }

    /**
     * Get new child select options
     *
     * @return array
     */
    public function getNewChildSelectOptions()
    {
        $orderAttributes = $this->_conditionOrder->loadAttributeOptions()->getAttributeOption();
        $attributes = [];
        foreach ($orderAttributes as $code => $label) {
            $attributes[] = [
                'value' => 'Synchrony\DigitalBuy\Model\Rule\Condition\Order|' . $code,
                'label' => $label,
            ];
        }

        $conditions = parent::getNewChildSelectOptions();
        $conditions = array_merge_recursive(
            $conditions,
            [
                [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Product\Found::class,
                    'label' => __('Product attribute combination'),
                ],
                [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Product\Subselect::class,
                    'label' => __('Products subselection')
                ],
                [
                    'value' => \Synchrony\DigitalBuy\Model\Rule\Condition\Combine::class,
                    'label' => __('Conditions combination')
                ],
                ['label' => __('Cart Attribute'), 'value' => $attributes]
            ]
        );

        $additional = new \Magento\Framework\DataObject();
        $this->_eventManager->dispatch('synchrony_promotion_condition_combine', ['additional' => $additional]);
        $additionalConditions = $additional->getConditions();
        if ($additionalConditions) {
            $conditions = array_merge_recursive($conditions, $additionalConditions);
        }

        return $conditions;
    }
}
