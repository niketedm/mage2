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

namespace Anowave\Faq\Ui\Component\Listing\Columns;

use Magento\Ui\Component\Listing\Columns\Column;

/**
 * Class ReviewActions
 *
 * @api
 * @since 100.1.0
 */
class FaqActions extends Column
{
	/**
	 * {@inheritdoc}
	 * @since 100.1.0
	 */
	public function prepareDataSource(array $dataSource)
	{
		$dataSource = parent::prepareDataSource($dataSource);
		
		if (empty($dataSource['data']['items'])) 
		{
			return $dataSource;
		}
		
		foreach ($dataSource['data']['items'] as &$item) 
		{
			$item[$this->getData('name')]['delete'] = 
			[
				'href' 		=> $this->context->getUrl('faq/index/delete',['id' => $item['faq_id'], 'productId' => $item['faq_product_id']]),
				'label' 	=> __('Delete'),
				'confirm' => 
				[
					'title' => __('Delete'),
					'message' => __('Are you sure you want to delete this FAQ?')
				],
				'hidden' 	=> false
			];
			
			$item[$this->getData('name')]['up'] =
			[
				'href' 		=> $this->context->getUrl('faq/index/up',['id' => $item['faq_id'], 'productId' => $item['faq_product_id']]),
				'label' 	=> __('Move up'),
				'hidden' 	=> false
			];
			
			$item[$this->getData('name')]['down'] =
			[
				'href' 		=> $this->context->getUrl('faq/index/down',['id' => $item['faq_id'], 'productId' => $item['faq_product_id']]),
				'label' 	=> __('Move down'),
				'hidden' 	=> false
			];
			
			$item[$this->getData('name')]['enable'] =
			[
				'href' 		=> $this->context->getUrl('faq/index/enable',['id' => $item['faq_id'], 'productId' => $item['faq_product_id']]),
				'label' 	=> __('Enable'),
				'hidden' 	=> false
			];
			
			$item[$this->getData('name')]['disable'] =
			[
				'href' 		=> $this->context->getUrl('faq/index/disable',['id' => $item['faq_id'], 'productId' => $item['faq_product_id']]),
				'label' 	=> __('Disable'),
				'hidden' 	=> false
			];
		}
		
		return $dataSource;
	}
}
