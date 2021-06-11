<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Import;

use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;
use Mancini\ShippingZone\Model\ShippingZone;
use Mancini\ShippingZone\Model\ShippingZone\Zipcodes;

class DownloadZipcodes extends AbstractAction
{
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var ShippingZone $model */
        $model = $this->_objectManager->create('Mancini\ShippingZone\Model\ShippingZone');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Shipping Zone no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/index/');
            }
        }

        /** start csv content and set template */
        $content = '"zipcode","city","state","is_delete"';
        $content .= "\n";

        $collection = $this->zipcodesFactory->create()->getCollection()->addFieldToFilter('zone_id', $model->getId());
        foreach ($collection as $zipcode) {
            /** @var Zipcodes $zipcode */
            $content .= '"' . $zipcode->getZipcode() . '","' . $zipcode->getCity() . '","' . $zipcode->getState() . '",""' . "\n";
        }
        $fileName = preg_replace('/\s+/i', '-', $model->getZoneName());
        return $this->fileFactory->create($fileName . '.csv', $content, DirectoryList::VAR_DIR);
    }
}
