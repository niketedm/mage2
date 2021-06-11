<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GdprConsent\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const CONSENT_COOKIE_NAME = 'gdpr_cookie_consent';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('gdpr/general/is_enabled');
    }

    public function getConsentCheckboxText()
    {
        return $this->scopeConfig->getValue('gdpr/consent_checkbox/checkbox_text');
    }

    /**
     * @param string $type
     *
     * @return bool
     */
    public function isConsentCheckboxEnabled($type)
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/consent_checkbox/' . $type . '_is_enabled');
    }

}
