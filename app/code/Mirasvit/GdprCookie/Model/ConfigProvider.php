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



namespace Mirasvit\GdprCookie\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class ConfigProvider
{
    const CONSENT_COOKIE_NAME = 'gdpr_cookie_consent';

    const MST_COOKIE_GROUPS = 'gdpr_cookie_groups';

    private $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
    }

    public function isCookieBarEnabled()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/cookie/is_enabled');
    }

    public function isEnabled()
    {
        return $this->scopeConfig->isSetFlag('gdpr/general/is_enabled');
    }

    public function isCookieBarLockScreen()
    {
        return $this->isEnabled() && $this->scopeConfig->isSetFlag('gdpr/cookie/bar/is_lock_screen');
    }

    public function getCookieBarPolicyText()
    {
        return $this->scopeConfig->getValue('gdpr/cookie/bar/content');
    }

    public function getCookieModalTitle()
    {
        return $this->scopeConfig->getValue('gdpr/cookie/modal/title');
    }

    public function getCookieModalText()
    {
        return $this->scopeConfig->getValue('gdpr/cookie/modal/content');
    }
}
