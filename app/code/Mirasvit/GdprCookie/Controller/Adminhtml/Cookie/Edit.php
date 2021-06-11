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



namespace Mirasvit\GdprCookie\Controller\Adminhtml\Cookie;

use Magento\Backend\Model\View\Result\Page\Interceptor;
use Magento\Framework\Controller\ResultFactory;
use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Controller\Adminhtml\AbstractCookie;

class Edit extends AbstractCookie
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        /** @var Interceptor $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);

        $id    = $this->getRequest()->getParam(CookieInterface::ID);
        $model = $this->initModel();

        if ($id && !$model->getId()) {
            $this->messageManager->addErrorMessage(__('This cookie no longer exists.'));

            return $this->resultRedirectFactory->create()->setPath('*/*/');
        }

        $this->initPage($resultPage)->getConfig()->getTitle()
            ->prepend($id ? $model->getName() : __('New Cookie'));


        return $resultPage;
    }
}
