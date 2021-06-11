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

namespace Anowave\Faq\Controller\Adminhtml\Index;

class Delete extends Position
{
	/**
	 * Execute 
	 * 
	 * {@inheritDoc}
	 * @see \Magento\Framework\App\ActionInterface::execute()
	 */
	public function execute()
	{
		$model = $this->getModel();
		
		if ($model->getId())
		{
			try 
			{
				/**
				 * Get product id
				 * 
				 * @var int $product
				 */
				$product = $model->getFaqProductId();
				
				/**
				 * Delete model
				 */
				$model->delete();
				
				/**
				 * Normalize sorting
				 */
				$position = 0;
				
				/**
				 * Get collection 
				 * 
				 * @var \Anowave\Faq\Model\ResourceModel\Item\Collection $collection
				 */
				$collection = $this->collectionFactory->create()->addFieldToFilter('faq_product_id', $product);
				
				foreach ($this->collectionFactory->create()->setOrder('faq_position','asc') as $item)
				{
					$item->setFaqPosition($position);
					$item->save();
					
					$position++;
				}
				
				/**
				 * Show confirmation
				 */
				$this->getMessageManager()->addSuccessMessage('FAQ deleted successfully');
			}
			catch (\Exception $e)
			{
				$this->getMessageManager()->addErrorMessage
				(
					$e->getMessage()
				);
			}
		}
		
		return $this->redirectBack();
	}
}
