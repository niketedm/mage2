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
use Mirasvit\LayeredNavigation\Service\Filter\FilterRatingService;

class RatingDataMapper
{
    /**
     * @var ExtraFiltersConfig
     */
    private $filterConfig;

    private $ratingFilterService;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource,
        FilterRatingService $ratingFilterService,
        ExtraFiltersConfig $filterConfig
    ) {
        $this->filterConfig        = $filterConfig;
        $this->ratingFilterService = $ratingFilterService;
        $this->resource            = $resource;
    }

    /**
     * @param array                                         $documents
     * @param \Magento\Framework\Search\Request\Dimension[] $dimensions
     * @param string                                        $indexIdentifier
     *
     * @return array
     */
    public function map(array $documents, $dimensions, $indexIdentifier)
    {
        if (!$this->filterConfig->isRatingFilterEnabled()) {
            return $documents;
        }

        if (is_array($dimensions)) {
            $scope = $this->ratingFilterService->getCurrentScope($dimensions);
        } else {
            $scope = $dimensions;
        }

        $table = new \Magento\Framework\DB\Ddl\Table();
        $table->setName($this->resource->getTableName('catalog_product_entity'));

        $select = $this->ratingFilterService->createRatingFilterSelectWithIds($scope, $table);
        $select->where('entities.entity_id IN(?)', array_keys($documents));

        $rows = [];
        foreach ($this->resource->getConnection()->fetchAll($select) as $product) {
            $rows[$product['entity_id']] = (int)$product['value']; // for "range" filter value should be integer
        }

        foreach ($documents as $id => $doc) {
            $doc[ExtraFiltersConfig::RATING_FILTER]          = isset($rows[$id]) ? $rows[$id] : 0;
            $doc[ExtraFiltersConfig::RATING_FILTER . '_raw'] = isset($rows[$id]) ? $rows[$id] : 0;
            $documents[$id]                                  = $doc;
        }

        return $documents;
    }
}
