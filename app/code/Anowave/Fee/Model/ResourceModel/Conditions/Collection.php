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
 
namespace Anowave\Fee\Model\ResourceModel\Conditions;

class Collection extends \Magento\Rule\Model\ResourceModel\Rule\Collection\AbstractCollection
{
	/**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    protected $date;
 
    /**
     * Constructor 
     * 
     * @param \Magento\Framework\Data\Collection\EntityFactory $entityFactory
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date
     * @param \Magento\Framework\DB\Adapter\AdapterInterface $connection
     * @param \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource
     */
    public function __construct
    (
        \Magento\Framework\Data\Collection\EntityFactory $entityFactory,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\Data\Collection\Db\FetchStrategyInterface $fetchStrategy,
        \Magento\Framework\Event\ManagerInterface $eventManager,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $date,
        \Magento\Framework\DB\Adapter\AdapterInterface $connection = null,
        \Magento\Framework\Model\ResourceModel\Db\AbstractDb $resource = null
    ) 
    {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        
        $this->date = $date;
    }

	/**
	 * Constructor 
	 * 
	 * @see \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection::_construct()
	 */
	protected function _construct()
	{
		$this->_init('Anowave\Fee\Model\Conditions', 'Anowave\Fee\Model\ResourceModel\Conditions');
	}
	
	/**
	 * Filter collection by specified date.
	 * Filter collection to only active rules.
	 *
	 * @param string|null $now
	 * @use $this->addStoreGroupDateFilter()
	 * @return $this
	 */
	public function setValidationFilter($now = null)
	{
		if (!$this->getFlag('validation_filter')) 
		{
			$this->addDateFilter($now);
			$this->addIsActiveFilter();
			$this->setOrder('sort_order', self::SORT_ORDER_DESC);
			$this->setFlag('validation_filter', true);
		}
	
		return $this;
	}
	
	/**
	 * From date or to date filter
	 *
	 * @param $now
	 * @return $this
	 */
	public function addDateFilter($now)
	{
		$this->getSelect()->where('from_date is null or from_date <= ?',$now)->where('to_date is null or to_date >= ?',$now);
	
		return $this;
	}
}