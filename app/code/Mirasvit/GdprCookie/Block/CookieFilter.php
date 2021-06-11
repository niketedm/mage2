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
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie\CollectionFactory as CookieCollectionFactory;

class CookieFilter extends Template
{
    private $configProvider;

    private $cookieCollectionFactory;

    public function __construct(
        ConfigProvider $configProvider,
        CookieCollectionFactory $cookieCollectionFactory,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider = $configProvider;

        $this->cookieCollectionFactory = $cookieCollectionFactory;
    }

    public function getPolicyText()
    {
        return $this->configProvider->getCookieBarPolicyText();
    }

    public function getJsonConfig()
    {
        $groups = [];

        $allCookies = $this->cookieCollectionFactory->create();
        /** @var \Mirasvit\GdprCookie\Model\Cookie $cookie */
        foreach ($allCookies as $cookie) {
            $groups[$cookie->getGroupId()][] = $cookie->getCode();
        }

        return [
            '*' => [
                'Mirasvit_GdprCookie/js/cookieFilter' => [
                    'groups'            => $groups,
                    'groupCookieName'   => ConfigProvider::MST_COOKIE_GROUPS,
                    'consentCookieName' => ConfigProvider::CONSENT_COOKIE_NAME,
                ],
            ],
        ];
    }

    public function _toHtml()
    {
        if (!$this->configProvider->isCookieBarEnabled()) {
            return false;
        }

        return parent::_toHtml();
    }
}
