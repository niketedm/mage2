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

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Delete extends Action
{
    /**
     * @param Context       $context
     * @param PageFactory   $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->logger = $logger;
        $this->infoFactory = $infoFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $infoModel = $this->infoFactory->create();
                $infoModel->load($id);
                if ($infoModel) {
                    $infoModel->delete();
                }
                $this->messageManager->addSuccess(__("You have successfully deleted the spin info."));
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                $this->messageManager->addError(
                    __('Something went wrong while deleting the spin info. Please review the error log.')
                );
            }
            $this->_redirect('spintowin/*/index');
            return;
        }
    }
}
