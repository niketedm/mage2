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



namespace Mirasvit\GdprCookie\Controller\Adminhtml\CookieGroup;

use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Controller\Adminhtml\AbstractCookieGroup;

class Save extends AbstractCookieGroup
{

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(CookieGroupInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $model = $this->initModel();
            $model->setName($data[CookieGroupInterface::NAME])
                ->setIsRequired($data[CookieGroupInterface::IS_REQUIRED])
                ->setIsActive($data[CookieGroupInterface::IS_ACTIVE])
                ->setDescription($data[CookieGroupInterface::DESCRIPTION])
                ->setPriority($data[CookieGroupInterface::PRIORITY])
                ->setStoreIds($data[CookieGroupInterface::STORE_IDS]);

            try {
                $this->cookieGroupRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Cookie Group was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [CookieGroupInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [CookieGroupInterface::ID => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}
