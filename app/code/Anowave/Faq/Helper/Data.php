<?php
/**
 * Anowave Magento 2 Frequently Asked Questions
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


namespace Anowave\Faq\Helper;

use Anowave\Package\Helper\Package;

class Data extends \Anowave\Package\Helper\Package
{
	/**
	 * Package name
	 * 
	 * @var string
	 */
	protected $package = 'MAGE2-FAQ';
	
	/**
	 * Config path 
	 * 
	 * @var string
	 */
	protected $config = 'faq/general/license';
	
	/**
	 * @var \Magento\Framework\App\Request\Http
	 */
	protected $request;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Store\Model\StoreManagerInterface $storeManager
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Anowave\Faq\Helper\Data $request
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set store manager
		 *
		 * @var Magento\Store\Model\StoreManagerInterface $storeManager
		 */
		$this->storeManager = $storeManager;
	}
	
	/**
	 * Get current store id
	 *
	 * @return number
	 */
	public function getCurrentStoreId()
	{
		if ($this->request->getParam('store'))
		{
			return (int) $this->storeManager->getStore((int) $this->request->getParam('store'))->getId();
		}
		
		return (int) $this->storeManager->getStore()->getId();
	}
	
	public function getStoreManager()
	{
		return $this->storeManager;
	}
	
}