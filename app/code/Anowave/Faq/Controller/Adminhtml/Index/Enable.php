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

class Enable extends Position
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
				$model->setFaqEnable(1);
				$model->save();

				/**
				 * Show confirmation
				 */
				$this->getMessageManager()->addSuccessMessage('FAQ enabled successfully');
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
