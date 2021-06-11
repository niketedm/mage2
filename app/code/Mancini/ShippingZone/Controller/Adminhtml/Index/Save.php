<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Exception;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;

class Save extends AbstractAction
{
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        $zoneId = null;
        if (isset($params['id'])) {
            $zoneId = $params['id'] ?: null;
            unset($params['id']);
        }
        $shippingZone = $this->shippingZoneFactory->create()->load($zoneId);
      
        $shippingZone->addData($params);

        try {
            $shippingZone->save();
            $zoneId = $shippingZone->getId();
            $this->messageManager->addSuccess('%1 has been saved successfully.', $shippingZone->getZoneName());
        } catch (Exception $e) {
            $this->messageManager->addError($e->getMessage());
        }
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($zoneId) {
            $resultRedirect->setPath('shippingzone/index/edit', array('id' => $zoneId));
        } else {
            $resultRedirect->setPath('shippingzone/index/new');
        }

        return $resultRedirect;
    }
}
