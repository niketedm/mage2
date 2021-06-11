<?php
namespace Anowave\Fee\Model\ResourceModel\Fee\Grid;

use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\Event\ManagerInterface as EventManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Api\Search\AggregationInterface;
use Psr\Log\LoggerInterface;
use Anowave\Fee\Model\ResourceModel\Fee\Collection as FeeCollection;

class Collection extends FeeCollection implements SearchResultInterface
{
	/**
	 * Aggregations
	 *
	 * @var AggregationInterface
	 */
	protected $aggregations;
	
	/**
	 * Constructor 
	 * 
	 * @param EntityFactoryInterface $entityFactory
	 * @param LoggerInterface $logger
	 * @param FetchStrategyInterface $fetchStrategy
	 * @param EventManagerInterface $eventManager
	 * @param unknown $mainTable
	 * @param unknown $eventPrefix
	 * @param unknown $eventObject
	 * @param unknown $resourceModel
	 * @param AdapterInterface $connection
	 * @param AbstractDb $resource
	 * @param string $model
	 */
	public function __construct
	(
		EntityFactoryInterface $entityFactory,
		LoggerInterface $logger,
		FetchStrategyInterface $fetchStrategy,
		EventManagerInterface $eventManager,
		$mainTable,
		$eventPrefix,
		$eventObject,
		$resourceModel,
		AdapterInterface $connection = null,
		AbstractDb $resource = null,
		$model = 'Magento\Framework\View\Element\UiComponent\DataProvider\Document'
	)
	{
		parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
		
		$this->_eventPrefix = $eventPrefix;
		$this->_eventObject = $eventObject;
		$this->_init($model, $resourceModel);
		
		$this->setMainTable($mainTable);
	}
	
	
	/**
	 * @return AggregationInterface
	 */
	public function getAggregations()
	{
		return $this->aggregations;
	}
	
	/**
	 * @param AggregationInterface $aggregations
	 * @return $this
	 */
	public function setAggregations($aggregations)
	{
		$this->aggregations = $aggregations;
	}
	
	
	/**
	 * Retrieve all ids for collection
	 * Backward compatibility with EAV collection
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return array
	 */
	public function getAllIds($limit = null, $offset = null)
	{
		return $this->getConnection()->fetchCol($this->_getAllIdsSelect($limit, $offset), $this->_bindParams);
	}
	
	/**
	 * Create all ids retrieving select with limitation
	 * Backward compatibility with EAV collection
	 *
	 * @param int $limit
	 * @param int $offset
	 * @return \Magento\Eav\Model\Entity\Collection\AbstractCollection
	 */
	protected function _getAllIdsSelect($limit = null, $offset = null)
	{
		$idsSelect = clone $this->getSelect();
		
		$idsSelect->reset(\Magento\Framework\DB\Select::ORDER);
		$idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_COUNT);
		$idsSelect->reset(\Magento\Framework\DB\Select::LIMIT_OFFSET);
		$idsSelect->reset(\Magento\Framework\DB\Select::COLUMNS);
		
		$idsSelect->columns($this->getResource()->getIdFieldName(), 'main_table');
		$idsSelect->limit($limit, $offset);
		return $idsSelect;
	}
	
	/**
	 * Get search criteria.
	 *
	 * @return \Magento\Framework\Api\SearchCriteriaInterface|null
	 */
	public function getSearchCriteria()
	{
		return null;
	}
	
	/**
	 * Set search criteria.
	 *
	 * @param SearchCriteriaInterface $searchCriteria
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setSearchCriteria(SearchCriteriaInterface $searchCriteria = null)
	{
		return $this;
	}
	
	/**
	 * Get total count.
	 *
	 * @return int
	 */
	public function getTotalCount()
	{
		return $this->getSize();
	}
	
	/**
	 * Set total count.
	 *
	 * @param int $totalCount
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setTotalCount($totalCount)
	{
		return $this;
	}
	
	/**
	 * Set items list.
	 *
	 * @param \Magento\Framework\Api\ExtensibleDataInterface[] $items
	 * @return $this
	 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
	 */
	public function setItems(array $items = null)
	{
		return $this;
	}
}