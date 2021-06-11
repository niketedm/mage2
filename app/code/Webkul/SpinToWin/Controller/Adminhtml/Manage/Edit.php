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

use Magento\Framework\Locale\Resolver;
use Webkul\SpinToWin\Model\InfoFactory;
use Magento\Framework\Registry;

class Edit extends \Magento\Backend\App\Action
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    private $resultPageFactory;

    /**
     * @var \Webkul\SpinToWin\Model\InfoFactory
     */
    private $infoFactory;

    /**
     * @var \Magento\Framework\Registry
     */
    private $coreRegistry;

    /**
     * Rule
     *
     * @var \Magento\SalesRule\Model\Rule
     */
    public $ruleFactory;

    /**
     * Constructor
     *
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param InfoFactory $infoFactory
     * @param \Magento\SalesRule\Model\RuleFactory $ruleFactory
     * @param Registry $registry
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        InfoFactory $infoFactory,
        Registry $registry
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->infoFactory = $infoFactory;
        $this->coreRegistry = $registry;
        parent::__construct($context);
    }

    /**
     * Init actions
     * @return \Magento\Backend\Model\View\Result\Page
     */
    public function _initAction()
    {
        // load layout, set active menu and breadcrumbs
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->resultPageFactory->create();
        $resultPage->setActiveMenu('Webkul_SpinToWin::manage')
            ->addBreadcrumb(__('Lists'), __('Lists'))
            ->addBreadcrumb(__('Manage Campaign'), __('Manage Spin to Win Campaigns'));
        return $resultPage;
    }

    /**
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $id = (int)$this->getRequest()->getParam('id');
        $spininfo = $this->infoFactory->create();
        if ($id) {
            $spininfo->load($id);
            if (!$spininfo->getEntityId()) {
                $this->messageManager->addError(__('This campaign no longer exists.'));
                $this->_redirect('*/*/index');
                return;
            }
        }
        
        $this->coreRegistry->register('spininfo', $spininfo);
        /** @var \Magento\Backend\Model\View\Result\Page $resultPage */
        $resultPage = $this->_initAction();
        $resultPage->addBreadcrumb(
            $id ? __('Edit %1', $spininfo->getName()) : __('New Campaign'),
            $id ? __('Edit Info') : __('New Info')
        );
        $resultPage->getConfig()->getTitle()->prepend($id ?__('Edit %1', $spininfo->getName()) : __('New Campaign'));
        return $resultPage;
    }

    /**
     * check permission
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_SpinToWin::manage');
    }
}
