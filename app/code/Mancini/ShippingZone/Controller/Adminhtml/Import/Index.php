<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Import;

use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Page;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Registry;
use Magento\ImportExport\Controller\Adminhtml\Import as ImportController;

class Index extends ImportController
{
    /** @var Registry */
    protected $_coreRegistry;

    /**
     * @param Context $context
     * @param Registry $coreRegistry
     */
    public function __construct(
        Context $context,
        Registry $coreRegistry
    )
    {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Index action
     *
     * @return Page|Redirect
     */
    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->_objectManager->create('Mancini\ShippingZone\Model\ShippingZone');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This Shipping Zone no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('*/index/index');
            }
        }

        $this->_coreRegistry->register('shipping_zone', $model);

        $this->messageManager->addNotice(
            $this->_objectManager->get('Magento\ImportExport\Helper\Data')->getMaxUploadSizeMessage()
        );
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_ImportExport::system_convert_import');
        //$resultPage->getConfig()->getTitle()->prepend(__('Import/Export'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import Zipcodes for %1', $model->getZoneName()));
        $resultPage->addBreadcrumb(__('Import'), __('Import'));

        return $resultPage;
    }
}
