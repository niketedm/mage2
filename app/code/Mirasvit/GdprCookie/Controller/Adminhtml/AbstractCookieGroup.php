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



namespace Mirasvit\GdprCookie\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Repository\CookieGroupRepository;

abstract class AbstractCookieGroup extends Action
{
    protected $cookieGroupRepository;

    protected $context;

    public function __construct(
        CookieGroupRepository $cookieGroupRepository,
        Context $context
    ) {
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->context               = $context;

        parent::__construct($context);
    }

    /**
     * @param \Magento\Backend\Model\View\Result\Page $resultPage
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    protected function initPage($resultPage)
    {
        $resultPage->setActiveMenu('Magento_Customer::customer');
        $resultPage->getConfig()->getTitle()->prepend(__('GDPR'));

        return $resultPage;
    }

    /**
     * @return bool|false|CookieGroupInterface
     */
    public function initModel()
    {
        $model = $this->cookieGroupRepository->create();

        if ($this->getRequest()->getParam(CookieGroupInterface::ID)) {
            $model = $this->cookieGroupRepository->get($this->getRequest()->getParam(CookieGroupInterface::ID));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_GdprCookie::gdpr_cookie');
    }
}
