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
 * @package   mirasvit/module-sorting
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Sorting\Service;

use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\ResourceConnection;
use Mirasvit\Sorting\Api\Data\IndexInterface;

class ScoreFetcherService
{
    private $productCollectionFactory;

    private $resource;

    public function __construct(
        ProductCollectionFactory $productCollectionFactory,
        ResourceConnection $resource
    ) {
        $this->productCollectionFactory = $productCollectionFactory;
        $this->resource                 = $resource;
    }

    public function getProductsScoreList(array $productIds): array
    {
        if (!count($productIds)) {
            return [];
        }

        $select = $this->resource->getConnection()->select();
        $select->from(
            $this->resource->getTableName(IndexInterface::TABLE_NAME),
            '*'
        )
            ->where(IndexInterface::PRODUCT_ID . ' IN (?)', $productIds);

        $rows = $this->resource->getConnection()
            ->fetchAll($select);

        $result = [];
        foreach ($rows as $row) {
            $result[$row[IndexInterface::PRODUCT_ID]] = $row;
        }

        return $result;
    }
}
