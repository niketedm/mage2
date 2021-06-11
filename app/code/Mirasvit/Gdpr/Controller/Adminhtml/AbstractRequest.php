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



namespace Mirasvit\Gdpr\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Repository\RequestRepository;

abstract class AbstractRequest extends Action
{
    protected $requestRepository;

    protected $context;

    public function __construct(
        RequestRepository $requestRepository,
        Context $context
    ) {
        $this->requestRepository = $requestRepository;
        $this->context           = $context;

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
        $resultPage->getConfig()->getTitle()->prepend(__('Customers\' Requests'));

        return $resultPage;
    }

    /**
     * @return RequestInterface
     */
    public function initModel()
    {
        $model = $this->requestRepository->create();

        if ($this->getRequest()->getParam(RequestInterface::ID)) {
            $model = $this->requestRepository->get($this->getRequest()->getParam(RequestInterface::ID));
        }

        return $model;
    }

    /**
     * {@inheritdoc}
     */
    protected function _isAllowed()
    {
        return $this->context->getAuthorization()->isAllowed('Mirasvit_Gdpr::gdpr_request');
    }
}
