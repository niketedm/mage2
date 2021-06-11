<?php
namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Magento\Framework\Controller\Result\Forward;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;

class NewAction extends AbstractAction
{
    public function execute()
    {
        /** @var Forward $resultForward */
        $resultForward = $this->resultForwardFactory->create();
        return $resultForward->forward('edit');
    }
}
