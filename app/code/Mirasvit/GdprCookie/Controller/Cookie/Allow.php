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



namespace Mirasvit\GdprCookie\Controller\Cookie;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http;
use Magento\Framework\Session\SessionManagerInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\GdprCookie\Model\ConfigProvider;

class Allow extends Action
{

    private $cookieManager;

    private $cookieMetadataFactory;

    private $sessionManager;

    public function __construct(
        CookieManagerInterface $cookieManager,
        CookieMetadataFactory $cookieMetadataFactory,
        SessionManagerInterface $sessionManager,
        Context $context
    ) {
        parent::__construct($context);

        $this->cookieManager         = $cookieManager;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->sessionManager        = $sessionManager;
    }

    public function execute()
    {
        $meta = $this->cookieMetadataFactory->createPublicCookieMetadata()
            ->setDurationOneYear()
            ->setDomain($this->sessionManager->getCookieDomain())
            ->setPath($this->sessionManager->getCookiePath());

        $this->cookieManager->setPublicCookie(ConfigProvider::CONSENT_COOKIE_NAME, 1, $meta);

        $groupIds = $this->getRequest()->getParam('group_ids');
        if (!empty($groupIds)) {
            $groupIds = implode(',', array_filter($groupIds, function($v) {
                return (int)$v;
            }));

            $this->cookieManager->setPublicCookie(ConfigProvider::MST_COOKIE_GROUPS, $groupIds, $meta);
        }

        /** @var Http $response */
        $response = $this->getResponse();
        $response->representJson(SerializeService::encode([
            'success' => true,
        ]));
    }
}
