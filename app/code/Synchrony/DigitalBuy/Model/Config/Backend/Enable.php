<?php

namespace Synchrony\DigitalBuy\Model\Config\Backend;

/**
 * Validate Payment Method Enablement
 *
 */
class Enable extends \Magento\Framework\App\Config\Value
{
    /**
     * Actions Before save
     * Validate if both the payments are not enabled
     *
     * @return $this
     */
    public function beforeSave()
    {
        $isInstallmentEnabled = $this->getValue();
        $config = $this->getData('groups')['synchrony_digitalbuy'];
        $isRevolvingEnabled = $config['groups']['revolving']['fields']['active']['value'];
        if ($isInstallmentEnabled && $isRevolvingEnabled) {
            $message = __('Only one Synchrony Digital Buy payment method may be enabled at this time.');
            throw new \Magento\Framework\Exception\LocalizedException($message);
        }

        return $this;
    }
}
