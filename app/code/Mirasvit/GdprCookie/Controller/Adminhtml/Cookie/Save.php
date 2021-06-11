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

use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Controller\Adminhtml\AbstractCookie;

class Save extends AbstractCookie
{
    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function execute()
    {
        $id             = $this->getRequest()->getParam(CookieInterface::ID);
        $resultRedirect = $this->resultRedirectFactory->create();

        $data = $this->getRequest()->getPostValue();

        if ($data) {
            $model = $this->initModel();

            $model->setName($data[CookieInterface::NAME])
                ->setCode($data[CookieInterface::CODE])
                ->setActive($data[CookieInterface::IS_ACTIVE])
                ->setDescription($data[CookieInterface::DESCRIPTION])
                ->setLifetime($data[CookieInterface::LIFETIME])
                ->setGroupId($data[CookieInterface::GROUP_ID])
                ->setStoreIds($data[CookieInterface::STORE_IDS]);

            try {
                echo $model->getId();
                $this->cookieRepository->save($model);
                $this->messageManager->addSuccessMessage(__('Cookie was saved.'));

                if ($this->getRequest()->getParam('back') == 'edit') {
                    return $resultRedirect->setPath('*/*/edit', [CookieInterface::ID => $model->getId()]);
                }

                return $resultRedirect->setPath('*/*/');
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage($e->getMessage());

                return $resultRedirect->setPath('*/*/edit', [CookieInterface::ID => $id]);
            }
        } else {
            $resultRedirect->setPath('*/*/');
            $this->messageManager->addErrorMessage('No data to save.');

            return $resultRedirect;
        }
    }
}
