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



namespace Mirasvit\Gdpr\Controller\Adminhtml\Request;

use Magento\Backend\App\Action\Context;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Controller\Adminhtml\AbstractRequest;
use Mirasvit\Gdpr\Repository\RequestRepository;

class Approve extends AbstractRequest
{
    public function __construct(
        RequestRepository $requestRepository,
        Context $context
    ) {
        parent::__construct($requestRepository, $context);
    }

    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam(RequestInterface::ID);

        $resultRedirect = $this->resultRedirectFactory->create();

        $model = $this->initModel();
        if (!$model->getId() && $id) {
            $this->messageManager->addErrorMessage(__('This request no longer exists.'));

            return $resultRedirect->setPath('*/*/');
        }

        try {
            $model->setStatus(RequestInterface::STATUS_PROCESSING);
            $this->requestRepository->save($model);

            $this->messageManager->addSuccessMessage(__('The request was processed.'));

            return $this->context->getResultRedirectFactory()->create()->setPath('*/*/');
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());

            return $resultRedirect->setPath('*/*');
        }
    }
}
