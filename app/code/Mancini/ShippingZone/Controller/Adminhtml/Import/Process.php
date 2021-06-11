<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Import;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use Mancini\ShippingZone\Model\ShippingZone\Zipcodes;
use Mancini\ShippingZone\Model\ShippingZone\ZipcodesFactory;
use Mancini\ShippingZone\Model\ShippingZone;

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
        $data = $this->getRequest()->getPostValue();
        if (!isset($data['zone_id'])) {

        }
        $zoneId = $data['zone_id'];
        /** @var ShippingZone $shippingZone */
        $shippingZone = $this->_objectManager->create('Mancini\ShippingZone\Model\ShippingZoneFactory')->create()->load($zoneId);
        if (!$shippingZone->getId()) {
            $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
            $resultRedirect->setPath('shippingzone/index/index');
            return $resultRedirect;
        }
        $data['entity'] = 'catalog_product';
        if ($data) {
            /** @var Import $import */
            $import = $this->getImport()->setData($data);
            try {
                $source = ImportAdapter::findAdapterFor(
                    $import->uploadSource(),
                    $this->_objectManager->create('Magento\Framework\Filesystem')
                        ->getDirectoryWrite(DirectoryList::ROOT),
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
                        /** @var ZipcodesFactory $zipcodesFactory */
                        $zipcodesFactory = $this->_objectManager->get('Mancini\ShippingZone\Model\ShippingZone\ZipcodesFactory');
                        $isDelete = ($rowData['is_delete'] == 1) ? true : false;
                        $zipcodesCollection = $zipcodesFactory->create()->getCollection()->addFieldToFilter('zipcode', $rowData['zipcode']);
                        /** @var Zipcodes $zipcode */
                        $zipcode = $zipcodesCollection->getFirstItem();
                        if ($zipcode->getId()) {
                            if ($isDelete) {
                                $zipcode->delete();
                            } else {
                                $zipcode->setData('zipcode', $rowData['zipcode']);
                                $zipcode->setData('city', $rowData['city']);
                                $zipcode->setData('state', $rowData['state']);
                                $zipcode->setData('zone_id', $zoneId);
                                $zipcode->save();
                            }
                        } else {
                            $zipcode = $zipcodesFactory->create()->load(null);
                            $zipcode->setData('zipcode', $rowData['zipcode']);
                            $zipcode->setData('city', $rowData['city']);
                            $zipcode->setData('state', $rowData['state']);
                            $zipcode->setData('zone_id', $zoneId);
                            $zipcode->save();
                        }
                    }
                    $this->messageManager->addSuccess('Import Zipcodes for %1 successfully.', $shippingZone->getZoneName());
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('shippingzone/import/index', array('id' => $zoneId));
                return $resultRedirect;
            }
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->messageManager->addError(__('The file was not uploaded.'));
        }
        $this->messageManager->addError(__('Sorry, but the data is invalid or the file is not uploaded.'));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('shippingzone/import/index', array('id' => $zoneId));
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
