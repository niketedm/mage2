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



namespace Mirasvit\GdprCookie\Block\Adminhtml;

use Magento\Backend\Block\Template\Context;
use Mirasvit\Core\Block\Adminhtml\AbstractMenu;

class Menu extends AbstractMenu
{

    public function __construct(
        Context $context
    ) {
        $this->visibleAt(['gdpr_cookie']);
        parent::__construct($context);
    }

    /**
     * {@inheritdoc}
     */
    protected function buildMenu()
    {
        $this->addItem([
            'resource' => 'Mirasvit_Gdpr::gdpr_cookie_cookies',
            'title'    => __('Cookies'),
            'url'      => $this->urlBuilder->getUrl('gdpr_cookie/cookie'),
        ])->addItem([
            'resource' => 'Mirasvit_Gdpr::gdpr_cookie_groups',
            'title'    => __('Cookie Groups'),
            'url'      => $this->urlBuilder->getUrl('gdpr_cookie/cookiegroup'),
        ])->addItem([
            'resource' => 'Mirasvit_Gdpr::gdpr_cookie_consents',
            'title'    => __('Cookie Consents'),
            'url'      => $this->urlBuilder->getUrl('gdpr_cookie/cookieconsent'),
        ]);

        $this->addSeparator();

        $this->addItem([
            'resource' => 'Mirasvit_GdprCookie::gdpr_cookie_settings',
            'title'    => __('Settings'),
            'url'      => $this->urlBuilder->getUrl('adminhtml/system_config/edit/section/gdpr'),
        ]);


        return $this;
    }
}
