<?php
namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

class Edit extends \Mancini\ShippingZone\Controller\Adminhtml\AbstractAction
{
    /**
     * say admin text
     */
    public function execute()
    {
        // 1. Get ID and create model
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Mancini\ShippingZone\Model\ShippingZone');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This block no longer exists.'));
                /** @var \Magento\Backend\Model\View\Result\Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/*/');
            }
        }

        $this->coreRegistry->register('shipping_zone', $model);

        // 5. Build edit form
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Mancini_ShippingZone::manage_shipping_zone')->addBreadcrumb(
            $id ? __('Edit Shipping Zone') : __('New Shipping Zone'),
            $id ? __('Edit Shipping Zone') : __('New Shipping Zone')
        );
        $resultPage->getConfig()->getTitle()->prepend(__('Shipping Zones'));
        $resultPage->getConfig()->getTitle()->prepend($model->getId() ? $model->getZoneName() : __('New Shipping Zone'));
        return $resultPage;
    }
}
?>
