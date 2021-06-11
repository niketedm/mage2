<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Zone;

use Magento\Backend\Model\View\Result\Page;
use Magento\Framework\Registry;
use Magento\ImportExport\Controller\Adminhtml\Import as ImportController;
use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;

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
    ) {
        parent::__construct($context);
        $this->_coreRegistry = $coreRegistry;
    }

    /**
     * Index action
     *
     * @return Page
     */
    public function execute()
    {
        $this->messageManager->addNotice(
            $this->_objectManager->get('Magento\ImportExport\Helper\Data')->getMaxUploadSizeMessage()
        );
        /** @var Page $resultPage */
        $resultPage = $this->resultFactory->create(ResultFactory::TYPE_PAGE);
        $resultPage->setActiveMenu('Magento_ImportExport::system_convert_import');
        //$resultPage->getConfig()->getTitle()->prepend(__('Import/Export'));
        $resultPage->getConfig()->getTitle()->prepend(__('Import Shipping Zones'));
        $resultPage->addBreadcrumb(__('Import Shipping Zones'), __('Import Shipping Zones'));

        return $resultPage;
    }
}
