<?php
/**
 * Anowave Magento 2 Frequenlty Asked Questions
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

namespace Anowave\Faq\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

/**
 * Google Analytics module observer
 *
 */
class Product implements ObserverInterface
{
	protected $factory = null;
	protected $request = null;
	
	public function __construct
	(
		\Anowave\Faq\Model\ItemFactory $factory,
		\Magento\Framework\App\Request\Http $request
	)
	{
		$this->factory = $factory;
		$this->request = $request;
	}
	
	public function execute(EventObserver $observer)
	{
		$sort = (array) $this->request->getParam('faq_sort');
		
		/**
		 * Remove first key
		 */
		array_shift($sort);
		
		$collection = $this->factory->create()->getCollection()->addProduct
		(
			(int) $this->request->getParam('faq_product_id')
		);
		
		foreach ($collection as $item) 
		{
			/**
			 * Get sort index
			 */
			$index = array_shift($sort);
			
			$item->setFaqSort($index);
			$item->save();
		}
		
		return true;
	}
}
