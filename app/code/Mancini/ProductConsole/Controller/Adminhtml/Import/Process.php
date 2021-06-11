<?php
namespace Mancini\ProductConsole\Controller\Adminhtml\Import;

use Exception;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\ImportExport\Controller\Adminhtml\ImportResult as ImportResultController;
use Magento\ImportExport\Model\Import;
use Magento\ImportExport\Model\Import\Adapter as ImportAdapter;
use Mancini\ProductConsole\Model\ImportSource;

class Process extends ImportResultController
{
    /** @var Import */
    protected $import;

    /** @var ImportSource */
    protected $sourceImport;

    /**
     * Validate uploaded files action
     *
     * @return ResultInterface
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $data['entity'] = 'catalog_product';
        if ($data) {
            /** @var $import Import */
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
                    $sourceImport = $this->getSourceImport();
                    $errorLog = $sourceImport->importSource($source);
                    if ($errorLog) {
                        $errorArray = explode("\n", $errorLog);
                        foreach ($errorArray as $line) {
                            if (!empty($line)) {
                                $this->messageManager->addError($line);
                            }
                        }
                    }
                } catch (Exception $e) {
                    $this->messageManager->addError($e->getMessage());
                }

                $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
                $resultRedirect->setPath('product_console/import/index');
                return $resultRedirect;
            }
        } elseif ($this->getRequest()->isPost() && empty($_FILES)) {
            $this->messageManager->addError(__('The file was not uploaded.'));
        }
        $this->messageManager->addError(__('Sorry, but the data is invalid or the file is not uploaded.'));
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $resultRedirect->setPath('product_console/import/index');
        return $resultRedirect;
    }

    public function processingImport($source)
    {
        // @todo: do we need this method?
    }

    private function getSourceImport()
    {
        if (!$this->sourceImport) {
            $this->sourceImport = $this->_objectManager->get('Mancini\ProductConsole\Model\ImportSource');
        }
        return $this->sourceImport;
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
