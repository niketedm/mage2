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

namespace Webkul\SpinToWin\Controller\Adminhtml\Report;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class Gridreset extends \Magento\Backend\App\Action
{
    protected $resultLayoutFactory;

    /**
     * @param Context       $context
     * @param PageFactory   $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\LayoutFactory $resultLayoutFactory
    ) {
        parent::__construct($context);
        $this->resultLayoutFactory = $resultLayoutFactory;
    }

    /**
     * List page.
     *
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function execute()
    {
        
        if ($this->getRequest()->isAjax()) {
            $resultLayout = $this->resultLayoutFactory->create();
            return $resultLayout;
        } else {
            $spinId = $this->getRequest()->getParam('spin_id');
            $this->_redirect('*/manage/edit', ['id' =>$spinId]);
        }
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

    public function _processUrlKeys()
    {
        return true;
    }
}
