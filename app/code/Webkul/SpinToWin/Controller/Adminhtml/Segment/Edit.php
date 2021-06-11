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

namespace Webkul\SpinToWin\Controller\Adminhtml\Segment;

use Magento\Framework\Locale\Resolver;
use Webkul\SpinToWin\Model\InfoFactory;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    protected $resultLayoutFactory;

    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory
    ) {
        parent::__construct($context);
        $this->_resultPageFactory = $resultPageFactory;
    }

    public function execute()
    {
        $resultPage = $this->_resultPageFactory->create();
        $output = $resultPage->getLayout()
                            ->createBlock('Webkul\SpinToWin\Block\Adminhtml\Segment\Edit')
                            ->toHtml();
        $this->getResponse()->setBody($output);
    }

    /**
     * check permission
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
