<?php

/**
 * Responsible for loading page content.
 *
 * This is a basic controller that only loads the corresponding layout file.
 *
 */
namespace Mancini\PowerReviews\Controller\Reviewpage;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
    /**
     * @var PageFactory
     */
    protected $_resultPageFactory;
    /**
     * @var JsonFactory
     */
    protected $_resultJsonFactory;
    /**
     * @param Context $context
     * @param PageFactory $resultPageFactory
     * @param JsonFactory $resultJsonFactory
     */
    public function __construct (
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $resultJsonFactory
    ) {
        $this->_resultPageFactory   =   $resultPageFactory;
        $this->_resultJsonFactory   =   $resultJsonFactory;

        parent::__construct($context);
    }

    /**
     *
     * @return \Magento\Framework\View\Result\Page
     */
    public function execute()
    {
        $result =   $this->_resultJsonFactory->create();
        $resultPage =   $this->_resultPageFactory->create();
        $currentPageId = $this->getRequest()->getParam('currentpage');
        $currentSKU = $this->getRequest()->getParam('prdsku');

        $data   =    array('currentpageid' => $currentPageId, 'prdsku' =>$currentSKU);

        $block  =   $resultPage->getLayout()
                    ->createBlock('Mancini\PowerReviews\Block\Reviewpaging')
                    ->setTemplate('Mancini_PowerReviews::reviewpage.phtml')
                    ->setData('data', $data)
                    ->toHtml();

        $result->setData(['output'=>$block]);
        return $result;
    }
}
