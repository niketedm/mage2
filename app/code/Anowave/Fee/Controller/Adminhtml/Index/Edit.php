<?php
/**
 * Anowave Magento 2 Extra Fee
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
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Fee\Controller\Adminhtml\Index;

class Edit extends \Magento\Backend\App\Action
{
	const ADMIN_RESOURCE = 'Anowave_Fee::fee';
	
	/**
	 * @var \Magento\Framework\View\Result\PageFactory
	 */
	protected $resultPageFactory;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Action\Context $context
	 * @param \Magento\Framework\View\Result\PageFactory $resultPageFactory
	 */
	public function __construct
	(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\View\Result\PageFactory $resultPageFactory
	)
	{
		parent::__construct($context);
		
		$this->resultPageFactory = $resultPageFactory;
	}
	
	/**
	 * Fees grid
	 *
	 * @return void
	 */
	public function execute()
	{
		$resultPage = $this->resultPageFactory->create();
		
		/**
		 * Set active menu
		 */
		$resultPage->setActiveMenu('Anowave_Fee::fee');
		
		/**
		 * Get label 
		 * 
		 * @var string $label
		 */
		$label = null !== $this->getRequest()->getParam('id') ? 'Edit fee':'Create fee';
		
		/**
		 * Add breadcrumbs
		 */
		$resultPage->addBreadcrumb(__('Fees'), __('Fees'));
		$resultPage->addBreadcrumb(__($label), __($label));
		
		/**
		 * Set title
		 */
		$resultPage->getConfig()->getTitle()->prepend(__($label));
		
		return $resultPage;
	}
}