<?php

namespace Mancini\ShippingZone\Model\Plugin\Cart;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Quote\Api\Data\ShippingMethodInterface;

class ShippingMethodConverter
{
    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var ObjectManagerInterface */
    protected $objectManager;

    /**
     * ShippingMethodConverter constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param ObjectManagerInterface $objectManager
     */
    public function __construct(ScopeConfigInterface $scopeConfig, ObjectManagerInterface $objectManager)
    {
        $this->scopeConfig = $scopeConfig;
        $this->objectManager = $objectManager;
    }

    /**
     * @param \Magento\Quote\Model\Cart\ShippingMethodConverter $subject
     * @param ShippingMethodInterface $result
     * @return ShippingMethodInterface
     */
    public function afterModelToDataObject(
        \Magento\Quote\Model\Cart\ShippingMethodConverter $subject,
        ShippingMethodInterface $result
    ) {
        $extensionAttributes = $result->getExtensionAttributes();
        if ($extensionAttributes === null) {
            $extensionAttributes = $this->objectManager->create('Magento\Quote\Api\Data\ShippingMethodExtension');
        }

        $methodCode = $result->getMethodCode();
        $methodCarrierCode = $result->getCarrierCode();
        $methodDescription = $this->scopeConfig->getValue("carriers/$methodCarrierCode/{$methodCode}_description");
        $methodTooltip = $this->scopeConfig->getValue("carriers/$methodCarrierCode/{$methodCode}_tooltip");
        //$extensionAttributes->setMethodDescription($methodDescription);
        $extensionAttributes->setData('method_description', $methodDescription);
        //$extensionAttributes->setMethodTooltip($methodTooltip);
        $extensionAttributes->setData('method_tooltip', $methodTooltip);
        $result->setExtensionAttributes($extensionAttributes);

        return $result;
    }
}
