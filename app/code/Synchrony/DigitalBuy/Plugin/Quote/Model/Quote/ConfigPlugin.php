<?php

namespace Synchrony\DigitalBuy\Plugin\Quote\Model\Quote;

use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\Session;
use Synchrony\DigitalBuy\Model\ResourceModel\Rule as RuleResource;

class ConfigPlugin
{
    /**
     * @var RuleResource
     */
    protected $_ruleResource;

    /**
     * @param RuleResource $ruleResource
     */
    public function __construct(RuleResource $ruleResource)
    {
        $this->_ruleResource = $ruleResource;
    }

    /**
     * Append synchrony promo rule product attribute keys to quote item collection
     *
     * @param \Magento\Quote\Model\Quote\Config $subject
     * @param array $attributeKeys
     *
     * @return array
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function afterGetProductAttributes(\Magento\Quote\Model\Quote\Config $subject, array $attributeKeys)
    {
        $attributes = $this->_ruleResource->getActiveAttributes();
        foreach ($attributes as $attribute) {
            $attributeKeys[] = $attribute['attribute_code'];
        }
        return $attributeKeys;
    }
}
