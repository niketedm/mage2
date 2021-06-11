<?php
namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Magento\Backend\Model\View\Result\Page;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;

class Index extends AbstractAction
{
    /**
     * say admin text
     */
    public function execute()
    {
        if ($this->getRequest()->getQuery('ajax')) {
            $resultForward = $this->resultForwardFactory->create();
            $resultForward->forward('grid');
            return $resultForward;
        }
        /** @var Page $resultPage */
        $resultPage = $this->resultPageFactory->create();

        // Set active menu item
        $resultPage->setActiveMenu('Mancini_ShippingZone::manage_shipping_zone');
        $resultPage->getConfig()->getTitle()->prepend(__('Manage Shipping Zones'));

        /**
         * Add breadcrumb item
         */
        $resultPage->addBreadcrumb(__('Shipping Zones'), __('Shipping Zones'));
        $resultPage->addBreadcrumb(__('Manage Shipping Zones'), __('Manage Shipping Zones'));

        $this->_getSession()->unsShippingZonesData();

        return $resultPage;
    }
}
