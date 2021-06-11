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

namespace Anowave\Fee\Ui\Component\Listing\Column;

use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Magento\Framework\UrlInterface;

class FeeActions extends Column
{
	/**
	 * Edit route
	 * 
	 * @var string
	 */
	const FEE_URL_PATH_EDIT = 'fee/index/edit';
	
	/**
	 * Delete route
	 * 
	 * @var string
	 */
	const FEE_URL_PATH_DELETE = 'fee/index/delete';
	
	/** 
	 * @var UrlInterface 
	 */
	protected $urlBuilder;
	
	/**
	 * @var string
	 */
	private $editUrl;
	
	/**
	 * Constructor 
	 * 
	 * @param ContextInterface $context
	 * @param UiComponentFactory $uiComponentFactory
	 * @param UrlInterface $urlBuilder
	 * @param array $components
	 * @param array $data
	 * @param string $editUrl
	 */
	public function __construct
	(
		ContextInterface $context,
		UiComponentFactory $uiComponentFactory,
		UrlInterface $urlBuilder,
		array $components = [],
		array $data = [],
		$editUrl = self::FEE_URL_PATH_EDIT
	) 
	{
		$this->urlBuilder = $urlBuilder;
		$this->editUrl = $editUrl;
		
		parent::__construct($context, $uiComponentFactory, $components, $data);
	}
	
	/**
	 * Prepare Data Source
	 *
	 * @param array $dataSource
	 * @return array
	 */
	public function prepareDataSource(array $dataSource)
	{
		if (isset($dataSource['data']['items'])) 
		{
			foreach ($dataSource['data']['items'] as & $item) 
			{
				$name = $this->getData('name');
				
				if (isset($item['fee_id'])) 
				{
					$item[$name]['edit'] = 
					[
						'href' => $this->urlBuilder->getUrl($this->editUrl, ['id' => $item['fee_id']]),
						'label' => __('Edit')
					];
					$item[$name]['delete'] = 
					[
						'href' => $this->urlBuilder->getUrl(self::FEE_URL_PATH_DELETE, ['id' => $item['fee_id']]),
						'label' => __('Delete'),
						'confirm' => 
						[
							'title' => __('Delete "${ $.$data.fee_name }"'),
							'message' => __('Are you sure you want to delete "${ $.$data.fee_name }" fee?')
						]
					];
				}
			}
		}

		return $dataSource;
	}
}