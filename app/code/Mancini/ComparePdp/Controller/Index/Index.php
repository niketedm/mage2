<?php

namespace Mancini\ComparePdp\Controller\Index;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\View\Result\PageFactory;

Class Index extends Action {

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
     * @return \Magento\Framework\Controller\Result\Json
     */
    public function execute() {
        $result =   $this->_resultJsonFactory->create();
        $resultPage =   $this->_resultPageFactory->create();
        $currentProductId = $this->getRequest()->getParam('currentproduct');

        $data   =    array('currentproductid' => $currentProductId);

        $block  =   $resultPage->getLayout()
                    ->createBlock('Mancini\ComparePdp\Block\CompareProduct')
                    ->setTemplate('Mancini_ComparePdp::compareproduct.phtml')
                    ->setData('data', $data)
                    ->toHtml();

        $result->setData(['output'=>$block]);
        return $result;

    }
}