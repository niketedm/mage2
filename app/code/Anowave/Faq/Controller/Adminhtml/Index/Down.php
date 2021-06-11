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

class Down extends Position
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
		
		/**
		 * Get previosu ID 
		 * 
		 * @var int $previous
		 */
		$next = $this->getNextId($model);
		
		if ($next)
		{
			$this->swap($model->getId(), $next);
		}
		
		return $this->redirectBack();
	}
	
	private function getNextId(\Anowave\Faq\Model\Item $model)
	{
		if ($model->getId())
		{
			$sql = 'SELECT 
						faq_id FROM ' . $this->getConnection()->getTableName('ae_faq') . ' 
							WHERE 
								(faq_product_id = ' . $model->getFaqProductId() . ' AND faq_position > ' . $model->getFaqPosition() . ') 
							ORDER BY faq_position ASC 
						LIMIT 1';

			$id = (int) $this->getConnection()->fetchOne($sql);
			
			return $id;
		}
		
		return 0;
	}
}
