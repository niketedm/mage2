<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.0
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Service\Filter;

use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\App\ScopeResolverInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FilterNewService
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ResourceConnection
     */
    private $resource;
    /**
     * @var \Magento\Framework\Stdlib\DateTime\TimezoneInterface
     */
    private $localeDate;
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var Visibility
     */
    private $catalogProductVisibility;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var ScopeResolverInterface
     */
    private $scopeResolver;

    /**
     * FilterNewService constructor.
     * @param ScopeResolverInterface $scopeResolver
     * @param CollectionFactory $productCollectionFactory
     * @param Visibility $catalogProductVisibility
     * @param RequestInterface $request
     * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
     * @param ResourceConnection $resource
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        RequestInterface $request,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        ResourceConnection $resource,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeResolver            = $scopeResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->request                  = $request;
        $this->localeDate               = $localeDate;
        $this->resource                 = $resource;
        $this->storeManager             = $storeManager;
    }

    /**
     * @param string $currentScope
     * @param mixed $entityIdsTable
     * @return \Magento\Framework\DB\Select
     */
    public function createNewFilterSelect($currentScope, $entityIdsTable)
    {
        $productCollection = $this->prepareProductCollection($currentScope);
        $connection        = $this->resource->getConnection();
        $derivedTable      = $connection->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            [ExtraFiltersConfig::NEW_FILTER => $productCollection->getSelect()],
            ExtraFiltersConfig::NEW_FILTER . '.entity_id  = entities.entity_id',
            [
                'value' => new \Zend_Db_Expr('if('
                    . ExtraFiltersConfig::NEW_FILTER . '.entity_id is null, 0, 1)'),
            ]
        );

        $select = $connection->select()->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * @param string $currentScope
     * @param mixed $entityIdsTable
     * @return \Magento\Framework\DB\Select
     */
    public function createNewFilterSelectWithIds($currentScope, $entityIdsTable)
    {
        $productCollection = $this->prepareProductCollection($currentScope);
        $derivedTable      = $this->resource->getConnection()->select();
        $derivedTable->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            [ExtraFiltersConfig::NEW_FILTER => $productCollection->getSelect()],
            ExtraFiltersConfig::NEW_FILTER . '.entity_id  = entities.entity_id',
            [
                'value'     => new \Zend_Db_Expr('if('
                    . ExtraFiltersConfig::NEW_FILTER . '.entity_id is null, 0, 1)'),
                'entity_id' => 'entities.entity_id',
            ]
        );

        return $derivedTable;
    }

    /**
     * @return int[]
     */
    protected function getProductVisibility()
    {
        if ($this->request->getRouteName() == Config::IS_CATALOG_SEARCH) {
            return $this->catalogProductVisibility->getVisibleInSearchIds();
        }

        return $this->catalogProductVisibility->getVisibleInCatalogIds();
    }

    /**
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     *
     * @return int
     */
    public function getCurrentScope($dimensions)
    {
        if (isset($dimensions[0])) {
            $dimensions['scope'] = $dimensions[0];
        }

        return $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();
    }

    /**
     *  Magento\Catalog\Block\Product/NewProduct (_getProductCollection())
     *
     * @param \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection
     */
    public function addNewFilterInCollection($productCollection)
    {
        $todayStartOfDayDate = $this->localeDate->date()->setTime(0, 0, 0)->format('Y-m-d H:i:s');
        $todayEndOfDayDate   = $this->localeDate->date()->setTime(23, 59, 59)->format('Y-m-d H:i:s');

        $productCollection->addAttributeToFilter(
            'news_from_date',
            [
                'or' => [
                    0 => ['date' => true, 'to' => $todayEndOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ],
            ],
            'left'
        )->addAttributeToFilter(
            'news_to_date',
            [
                'or' => [
                    0 => ['date' => true, 'from' => $todayStartOfDayDate],
                    1 => ['is' => new \Zend_Db_Expr('null')],
                ],
            ],
            'left'
        )->addAttributeToFilter(
            [
                ['attribute' => 'news_from_date', 'is' => new \Zend_Db_Expr('not null')],
                ['attribute' => 'news_to_date', 'is' => new \Zend_Db_Expr('not null')],
            ]
        );
    }

    /**
     * @param mixed $select
     * @param int $storeId
     * @param int $newFilterValue
     */
    public function addNewToSelect($select, $storeId, $newFilterValue)
    {
        //show only correct filters (avoid "We can't find products matching the selection.")
        if ($newFilterValue == 1) {
            $productCollectionNew = $this->prepareProductCollection($storeId);
            $newFilter            = ExtraFiltersConfig::NEW_FILTER . '_filter';
            $select->joinRight(
                [$newFilter => $productCollectionNew->getSelect()],
                'search_index.entity_id = ' . $newFilter . '.entity_id',
                []
            );
        }
    }

    /**
     * Prepare product collection and return it.
     *
     * @param int $currentScope
     *
     * @return \Magento\Catalog\Model\ResourceModel\Product\Collection
     */
    private function prepareProductCollection($currentScope)
    {
        /** @var \Magento\Catalog\Model\ResourceModel\Product\Collection $productCollection */
        $productCollection = $this->productCollectionFactory->create()
            ->setStore($currentScope)
            ->setVisibility($this->getProductVisibility())
            ->addStoreFilter($currentScope);

        $this->addNewFilterInCollection($productCollection);
        $productCollection->getSelect()->reset(\Zend_Db_Select::COLUMNS);
        $productCollection->getSelect()->columns('e.entity_id');

        return $productCollection;
    }
}
