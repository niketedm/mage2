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



namespace Mirasvit\LayeredNavigation\Index\Mirasvit\LayeredNavigation;

use Magento\Framework\App\ResourceConnection;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Service\Filter\FilterOnSaleService;

class OnSaleDataMapper
{
    /**
     * @var ExtraFiltersConfig
     */
    private $filterConfig;

    private $onSaleFilterService;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * OnSaleDataMapper constructor.
     *
     * @param ResourceConnection  $resource
     * @param FilterOnSaleService $onSaleFilterService
     * @param ExtraFiltersConfig  $filterConfig
     */
    public function __construct(
        ResourceConnection $resource,
        FilterOnSaleService $onSaleFilterService,
        ExtraFiltersConfig $filterConfig
    ) {
        $this->filterConfig        = $filterConfig;
        $this->onSaleFilterService = $onSaleFilterService;
        $this->resource            = $resource;
    }

    /**
     * Populate index with on_sale values for products.
     * 1 - on sale, otherwise - 0.
     *
     * @param array                                         $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param string                                        $indexIdentifier
     *
     * @return array
     */
    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        if (!$this->filterConfig->isOnSaleFilterEnabled()) {
            return $documents;
        }

        if (is_array($dimensions)) {
            $scope = $this->onSaleFilterService->getCurrentScope($dimensions);
        } else {
            $scope = $dimensions;
        }

        $table = new \Magento\Framework\DB\Ddl\Table();
        $table->setName($this->resource->getTableName('catalog_product_entity'));

        $select = $this->onSaleFilterService->getOnSaleFilterSelectWithIds($scope, $table);
        $select->where(ExtraFiltersConfig::ON_SALE_FILTER . '.entity_id IN(?)', array_keys($documents));

        $rows = [];
        foreach ($this->resource->getConnection()->fetchAll($select) as $product) {
            $rows[$product['entity_id']] = $product['value'];
        }

        foreach ($documents as $id => $doc) {
            $doc[ExtraFiltersConfig::ON_SALE_FILTER]          = isset($rows[$id]) ? (int)$rows[$id] : 0;
            $doc[ExtraFiltersConfig::ON_SALE_FILTER . '_raw'] = isset($rows[$id]) ? (int)$rows[$id] : 0;

            $documents[$id] = $doc;
        }

        return $documents;
    }
}
