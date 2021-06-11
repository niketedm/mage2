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

namespace Anowave\Fee\Model\Fee;

use Anowave\Fee\Model\ResourceModel\Fee\CollectionFactory;

class DataProvider extends \Magento\Ui\DataProvider\AbstractDataProvider
{
	/**
	 * 
	 * @var Anowave\Fee\Model\ResourceModel\Fee\CollectionFactory
	 */
	protected $feeCollectionFactory;
	
	/**
	 * Constructor
	 * 
	 * @param string $name
	 * @param string $primaryFieldName
	 * @param string $requestFieldName
	 * @param CollectionFactory $contactCollectionFactory
	 * @param array $meta
	 * @param array $data
	 */
	public function __construct
	(
		$name,
		$primaryFieldName,
		$requestFieldName,
		CollectionFactory $feeCollectionFactory,
		array $meta = [],
		array $data = []
	) 
	{
		$this->collection = $feeCollectionFactory->create();
		
		parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
	}
	
	public function getData()
	{
		if (isset($this->loadedData)) 
		{
			return $this->loadedData;
		}
		
		$items = $this->collection->getItems();
		
		$this->loadedData = [];
		
		foreach ($items as $fee) 
		{
			$this->loadedData[$fee->getId()]['fee'] = $fee->getData();
		}
		
		return $this->loadedData;
	}
}
