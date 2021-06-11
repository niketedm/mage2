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
 
namespace Anowave\Faq\Model\ResourceModel\Item;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $factory = null;
	
	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory
	 * @param \Psr\Log\LoggerInterface $logger
	 * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
	 * @param \Magento\Framework\Event\ManagerInterface $eventManager
	 * @param \Magento\Catalog\Model\ProductFactory $factory
	 */
	public function __construct
	(
		\Magento\Framework\Data\Collection\EntityFactoryInterface $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
		\Magento\Catalog\Model\ProductFactory $factory
	)
	{
		$this->factory = $factory;
		
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager);
	}
	
	/**
	 * Joines templates information
	 *
	 * @return $this
	 */
	public function addProduct($product)
	{
		$this->addFieldToFilter('faq_product_id', $product);

		return $this;
	}
	
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Anowave\Faq\Model\Item', 'Anowave\Faq\Model\ResourceModel\Item');
    }
}