<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Controller\Adminhtml\Manage;

use Magento\Framework\Controller\ResultFactory;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Webkul\SpinToWin\Model\ResourceModel\Info\CollectionFactory;

class MassDelete extends \Magento\Backend\App\Action
{
    public $filter;

    public $collectionFactory;

    public $logger;

    public function __construct(
        Context $context,
        Filter $filter,
        CollectionFactory $collectionFactory,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->filter = $filter;
        $this->collectionFactory = $collectionFactory;
        $this->helper = $helper;
        $this->logger = $logger;
        parent::__construct($context);
    }

    /**
     * @return \Magento\Backend\Model\View\Result\Redirect
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
       
        try {
            $collection = $this->filter->getCollection($this->collectionFactory->create());
            $count = 0;
            foreach ($collection as $spins) {
                $this->deleteObject($spins);
                $count++;
            }
            $this->helper->cacheFlush();
            $this->messageManager->addSuccess(__('A total of %1 spin(s) have been deleted.', $count));
        } catch (\Exception $e) {
            $this->logger->info(
                "MassDelete::excute ".$e->getMessage()
            );
            $this->messageManager->addError(__($e->getMessage()));
        }
        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('*/manage/index');
    }

    /**
     * check permission
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_SpinToWin::manage');
    }

    /**
     * delete
     * @param Object $object
     * @return void
     */
    public function deleteObject($object)
    {
        $object->delete();
    }
}
