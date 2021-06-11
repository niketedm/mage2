<?php
/**
 * Anowave Magento 2 FAQ
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\Faq\Controller\Adminhtml\Index;

class Faq extends \Anowave\Faq\Controller\Adminhtml\Index
{
    /**
     * Faq Action
     *
     * @return \Magento\Framework\View\Result\Layout
     */
    public function execute()
    { 
    	$layout = $this->resultLayoutFactory->create();
    	
    	$layout->getLayout()->getBlock('faq')->setProductId((int) $this->getRequest()->getParam('id'))->setUseAjax(true);
    	
       	return $layout;
    }
}