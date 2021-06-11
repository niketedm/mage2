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



namespace Mirasvit\GdprCookie\Block;

use Magento\Framework\View\Element\Template;
use Mirasvit\GdprCookie\Model\ConfigProvider;

class CookieBar extends Template
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider = $configProvider;
    }

    public function getPolicyText()
    {
        return $this->configProvider->getCookieBarPolicyText();
    }

    public function getJsonConfig()
    {
        return [
            '*' => [
                'Mirasvit_GdprCookie/js/cookieBar' => [
                    'allowUrl'          => $this->getUrl('gdpr_cookie/cookie/allow'),
                    'lockScreen'        => $this->configProvider->isCookieBarLockScreen(),
                    'groupCookieName'   => ConfigProvider::MST_COOKIE_GROUPS,
                    'consentCookieName' => ConfigProvider::CONSENT_COOKIE_NAME,
                ],
            ],
        ];
    }

    public function getCookieModalText()
    {
        return $this->configProvider->getCookieModalText();
    }

    public function _toHtml()
    {
        if (!$this->configProvider->isCookieBarEnabled()) {
            return false;
        }

        return parent::_toHtml();
    }
}
