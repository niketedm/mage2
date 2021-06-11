<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Zone;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;

class Process extends ImportResultController
{
    /** @var Import */
    protected $import;

    /**
     * Validate uploaded files action
     *
     * @return ResultInterface
     */
    public function execute()
    {   
        $zoneCollection= '';
        $data = $this->getRequest()->getPostValue();
        $data['entity'] = 'catalog_product';
        if ($data) {
            /** @var $import Import */
            $import = $this->getImport()->setData($data);
            try {
                $directory = $this->_objectManager->create('Magento\Framework\Filesystem')
                    ->getDirectoryWrite(DirectoryList::ROOT);
                $source = ImportAdapter::findAdapterFor(
                    $import->uploadSource(),
                    $directory,
                    null
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (Exception $e) {
                $this->messageManager->addError(__('Sorry, but the data is invalid or the file is not uploaded.'));
            }

            if (isset($source)) {
                try {
                    foreach ($source as $rowNum => $rowData) {
                        $zoneFactory = $this->_objectManager->get('Mancini\ShippingZone\Model\ShippingZoneFactory');
                        $isDelete = ($rowData['is_delete'] == 1) ? true : false;
                        $zoneCollection = $zoneFactory->create()->getCollection()->addFieldToFilter('zone_name', array('like' => $rowData['zone_name']));
                        $existZone = $zoneCollection->getFirstItem();
                        if ($existZone->getId()) {
                            if ($isDelete) {
                                $existZone->delete();
                            } else {
                                $existZone->setData('standard_shipping_cost', $rowData['standard_shipping_cost']);
                                $existZone->setData('premium_shipping_cost', $rowData['premium_shipping_cost']);
                                $existZone->save();
                            }
                        } else {
                            $zone = $zoneFactory->create()->load(null);
                            $zone->setData('zone_name', $rowData['zone_name']);
                            $zone->setData('standard_shipping_cost', $rowData['standard_shipping_cost']);
                            $zone->setData('premium_shipping_cost', $rowData['premium_shipping_cost']);
                            $zone->save();
                        }
                    }

                    $this->messageManager->addSuccess("Import Shipping Zones successfully.");
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('shippingzone/index/index');
                return $resultRedirect;
            }
            //return $resultLayout;
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->messageManager->addError(__('The file was not uploaded.'));
        }
        $this->messageManager->addError(__('Sorry, but the data is invalid or the file is not uploaded.'));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('shippingzone/zone/index');

        return $resultRedirect;
    }

    public function processingImport($source)
    {
        // @todo: do we need this method?
    }

    /**
     * @return Import
     * @deprecated
     */
    private function getImport()
    {
        if (!$this->import) {
            $this->import = $this->_objectManager->get(Import::class);
        }
        return $this->import;
    }
}
