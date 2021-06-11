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



namespace Mirasvit\LayeredNavigation\Plugin\Frontend\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider;

use Magento\CatalogSearch\Model\Adapter\Mysql\Aggregation\DataProvider;
use Magento\Framework\DB\Ddl\Table;
use Magento\Framework\DB\Select;
use Magento\Framework\Search\Request\BucketInterface;
use Magento\Framework\Search\Request\Dimension;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Service\Filter\FilterNewService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterOnSaleService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterRatingService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterStockService;

class AdditionalFiltersSelect
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ExtraFiltersConfig
     */
    private $additionalFiltersConfig;
    /**
     * @var FilterRatingService
     */
    private $filterRatingService;
    /**
     * @var FilterStockService
     */
    private $filterStockService;
    /**
     * @var FilterOnSaleService
     */
    private $filterOnSaleService;
    /**
     * @var FilterNewService
     */
    private $filterNewService;

    /**
     * AdditionalFiltersSelect constructor.
     *
     * @param FilterNewService      $filterNewService
     * @param FilterOnSaleService   $filterOnSaleService
     * @param FilterStockService    $filterStockService
     * @param FilterRatingService   $filterRatingService
     * @param ExtraFiltersConfig    $additionalFiltersConfig
     * @param StoreManagerInterface $storeManager
     */
    public function __construct(
        FilterNewService $filterNewService,
        FilterOnSaleService $filterOnSaleService,
        FilterStockService $filterStockService,
        FilterRatingService $filterRatingService,
        ExtraFiltersConfig $additionalFiltersConfig,
        StoreManagerInterface $storeManager
    ) {
        $this->filterNewService        = $filterNewService;
        $this->filterOnSaleService     = $filterOnSaleService;
        $this->filterStockService      = $filterStockService;
        $this->filterRatingService     = $filterRatingService;
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager            = $storeManager;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @param DataProvider    $subject
     * @param \Closure        $proceed
     * @param BucketInterface $bucket
     * @param Dimension[]     $dimensions
     * @param Table           $entityIdsTable
     *
     * @return Select
     */
    public function aroundGetDataSet(
        DataProvider $subject,
        \Closure $proceed,
        BucketInterface $bucket,
        array $dimensions,
        Table $entityIdsTable
    ) {
        if ($bucket->getField() == ExtraFiltersConfig::NEW_FILTER
            && ($currentScope = $this->filterNewService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isNewFilterEnabled($currentScope)
        ) {
            return $this->filterNewService->createNewFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == ExtraFiltersConfig::ON_SALE_FILTER
            && ($currentScope = $this->filterNewService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isOnSaleFilterEnabled($currentScope)
        ) {
            return $this->filterOnSaleService->createOnSaleFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == ExtraFiltersConfig::STOCK_FILTER
            && ($currentScope = $this->filterStockService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isStockFilterEnabled($currentScope)
        ) {
            return $this->filterStockService->createStockFilterSelect($currentScope, $entityIdsTable);
        }

        if ($bucket->getField() == ExtraFiltersConfig::RATING_FILTER
            && ($currentScope = $this->filterRatingService->getCurrentScope($dimensions))
            && $this->additionalFiltersConfig->isRatingFilterEnabled($currentScope)
        ) {
            return $this->filterRatingService->createRatingFilterSelect($currentScope, $entityIdsTable);
        }

        return $proceed($bucket, $dimensions, $entityIdsTable);
    }
}
