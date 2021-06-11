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
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;

class FilterRatingService
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ResourceConnection
     */
    private $resourceConnection;

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
     * FilterRatingService constructor.
     *
     * @param ScopeResolverInterface $scopeResolver
     * @param CollectionFactory      $productCollectionFactory
     * @param Visibility             $catalogProductVisibility
     * @param RequestInterface       $request
     * @param ResourceConnection     $resourceConnection
     * @param StoreManagerInterface  $storeManager
     */
    public function __construct(
        ScopeResolverInterface $scopeResolver,
        CollectionFactory $productCollectionFactory,
        Visibility $catalogProductVisibility,
        RequestInterface $request,
        ResourceConnection $resourceConnection,
        StoreManagerInterface $storeManager
    ) {
        $this->scopeResolver            = $scopeResolver;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->catalogProductVisibility = $catalogProductVisibility;
        $this->request                  = $request;
        $this->resourceConnection       = $resourceConnection;
        $this->storeManager             = $storeManager;
    }

    /**
     * @param string $currentScope
     * @param mixed  $entityIdsTable
     *
     * @return \Magento\Framework\DB\Select
     */
    public function createRatingFilterSelect($currentScope, $entityIdsTable)
    {
        $derivedTable = $this->resourceConnection->getConnection()->select()->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            ['main_table' => $this->resourceConnection->getTableName('review_entity_summary')],
            '`main_table`.`entity_pk_value`=`entities`.entity_id
                AND `main_table`.entity_type = 1
                AND `main_table`.store_id  = ' . $currentScope,
            [
                'value' => new \Zend_Db_Expr('
                    if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=100,
                        5,
                        if(
                            main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=80,
                            4,
                            if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=60,
                                3,
                                if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=40,
                                    2,
                                    if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=20,
                                        1, 0
                                    )
                                )
                            )
                        )
                    )'),
            ]
        );

        $select = $this->resourceConnection->getConnection()->select();
        $select->from(['main_table' => $derivedTable]);

        return $select;
    }

    /**
     * We use it for elastic index.
     *
     * @param string $currentScope
     * @param mixed  $entityIdsTable
     *
     * @return \Magento\Framework\DB\Select
     */
    public function createRatingFilterSelectWithIds($currentScope, $entityIdsTable)
    {
        $derivedTable = $this->resourceConnection->getConnection()->select()->from(
            ['entities' => $entityIdsTable->getName()],
            []
        );

        $derivedTable->joinLeft(
            ['main_table' => $this->resourceConnection->getTableName('review_entity_summary')],
            '`main_table`.`entity_pk_value`=`entities`.entity_id
                AND `main_table`.entity_type = 1
                AND `main_table`.store_id  = ' . $currentScope,
            [
                'value'     => new \Zend_Db_Expr('
                    if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=100,
                        5,
                        if(
                            main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=80,
                            4,
                            if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=60,
                                3,
                                if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=40,
                                    2,
                                    if(main_table.' . ExtraFiltersConfig::RATING_FILTER . ' >=20,
                                        1, 0
                                    )
                                )
                            )
                        )
                    )'),
                'entity_id' => 'entities.entity_id',
            ]
        );

        return $derivedTable;
    }

    /**
     * @param array $dimensions
     *
     * @return int
     */
    public function getCurrentScope($dimensions)
    {
        return $this->scopeResolver->getScope($dimensions['scope']->getValue())->getId();
    }

    /**
     * @param mixed $select
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function addRatingToSelect($select)
    {
        $ratingFilter = ExtraFiltersConfig::RATING_FILTER . '_filter';
        $select->joinLeft(
            [$ratingFilter => $this->resourceConnection->getTableName('review_entity_summary')],
            $ratingFilter . '.store_id  = ' . $this->storeManager->getStore()->getId()
            . ' AND ' . $ratingFilter . '.entity_type = 1'
            . ' AND ' . $ratingFilter . '.entity_pk_value = search_index.entity_id',
            []
        );
    }
}
