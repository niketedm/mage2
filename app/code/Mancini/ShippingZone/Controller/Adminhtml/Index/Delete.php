<?php
namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Exception;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;

class Delete extends AbstractAction
{
    public function execute()
    {
        $zoneId = $this->getRequest()->getParam('id', false);
        if ($zoneId) {
            try {
                $shippingZone = $this->shippingZoneFactory->create()->load($zoneId);
                $shippingZoneName = $shippingZone->getName();
                $shippingZone->delete();
                $this->messageManager->addSuccess("Delete %1 successfully.", $shippingZoneName);
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('shippingzone/index/index');
                return $resultRedirect;
            } catch (Exception $e) {
                $this->messageManager->addError($e->getMessage());
                $resultRedirect = $this->resultRedirectFactory->create();
                $resultRedirect->setPath('shippingzone/index/edit', array('id' => $zoneId));
                return $resultRedirect;
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('shippingzone/index/index');
        return $resultRedirect;
    }
}
