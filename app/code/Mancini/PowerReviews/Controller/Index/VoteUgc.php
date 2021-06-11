<?php

/**
 * Mancini_PowerReviews
 */

namespace Mancini\PowerReviews\Controller\Index;

/**
 * Responsible for loading page content.
 *
 * This is a basic controller for adding the voting 
 *
 */
class VoteUgc extends \Magento\Framework\App\Action\Action
{
    /** @var \Magento\Framework\View\Result\PageFactory  */
    protected $resultPageFactory;
    /**
     * @var \Mancini\PowerReviews\Helper\Data
     */
    protected $powerreviewHelper;
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    protected $_messageManager;

    /**
     * @param \Magento\Framework\App\Action\Context $context
     * @param \Mancini\PowerReviews\Helper\Data $powerreviewHelper
     * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
     * @param \Magento\Framework\Message\ManagerInterface $messageManager
     */
    public function __construct(
        \Magento\Framework\App\Action\Context $context,
        \Mancini\PowerReviews\Helper\Data $powerreviewHelper,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Message\ManagerInterface $messageManager
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->powerreviewHelper = $powerreviewHelper;
        parent::__construct($context);
        $this->_messageManager = $messageManager;
    }
    /**
     * Load the page defined in view/frontend/layout/samplenewpage_index_index.xml
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result  =  $this->powerreviewHelper->voteUGC($this->getRequest()->getPost('ugcId'),$this->getRequest()->getPost('voteType'));
        $this->_messageManager->addSuccess('Successfully Posted... Will be updated in one day...');
        return($result);
    }
}
