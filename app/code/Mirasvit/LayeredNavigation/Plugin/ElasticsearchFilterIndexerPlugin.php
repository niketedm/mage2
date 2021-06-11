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



namespace Mirasvit\LayeredNavigation\Plugin;

use Mirasvit\LayeredNavigation\Index\Mirasvit\LayeredNavigation\NewProductsDataMapper;
use Mirasvit\LayeredNavigation\Index\Mirasvit\LayeredNavigation\OnSaleDataMapper;
use Mirasvit\LayeredNavigation\Index\Mirasvit\LayeredNavigation\RatingDataMapper;
use Mirasvit\LayeredNavigation\Index\Mirasvit\LayeredNavigation\StockDataMapper;

class ElasticsearchFilterIndexerPlugin
{
    private $newProductsDataMapper;

    private $onSaleDataMapper;

    private $ratingDataMapper;

    private $stockDataMapper;

    public function __construct(
        NewProductsDataMapper $newProductsDataMapper,
        OnSaleDataMapper $onSaleDataMapper,
        RatingDataMapper $ratingDataMapper,
        StockDataMapper $stockDataMapper
    ) {
        $this->newProductsDataMapper = $newProductsDataMapper;
        $this->onSaleDataMapper      = $onSaleDataMapper;
        $this->ratingDataMapper      = $ratingDataMapper;
        $this->stockDataMapper       = $stockDataMapper;
    }

    /**
     * @param mixed $subject
     * @param array $documents
     * @param int   $storeId
     * @param int   $mappedIndexerId
     *
     * @return array
     */
    public function beforeAddDocs($subject, array $documents, $storeId, $mappedIndexerId)
    {
        $documents = $this->newProductsDataMapper->map($documents, $storeId, $mappedIndexerId);
        $documents = $this->onSaleDataMapper->map($documents, $storeId, $mappedIndexerId);
        $documents = $this->ratingDataMapper->map($documents, $storeId, $mappedIndexerId);
        $documents = $this->stockDataMapper->map($documents, $storeId, $mappedIndexerId);

        return [$documents, $storeId, $mappedIndexerId];
    }
}
