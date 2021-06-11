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



namespace Mirasvit\LayeredNavigation\Plugin\Frontend\CatalogSearch\Model\Search\IndexBuilder;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Model\Config\ExtraFiltersConfig;
use Mirasvit\LayeredNavigation\Service\Filter\FilterNewService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterOnSaleService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterRatingService;
use Mirasvit\LayeredNavigation\Service\Filter\FilterStockService;

class AdditionalFiltersSelectBuilder
{
    /**
     * @var RequestInterface
     */
    private $request;
    /**
     * @var int
     */
    private $storeId;
    /**
     * @var FilterOnSaleService
     */
    private $filterOnSaleService;
    /**
     * @var FilterNewService
     */
    private $filterNewService;
    /**
     * @var FilterRatingService
     */
    private $filterRatingService;
    /**
     * @var FilterStockService
     */
    private $filterStockService;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var ExtraFiltersConfig
     */
    private $additionalFiltersConfig;

    /**
     * AdditionalFiltersSelectBuilder constructor.
     *
     * @param ExtraFiltersConfig    $additionalFiltersConfig
     * @param StoreManagerInterface $storeManager
     * @param FilterStockService    $filterStockService
     * @param FilterRatingService   $filterRatingService
     * @param FilterNewService      $filterNewService
     * @param FilterOnSaleService   $filterOnSaleService
     * @param RequestInterface      $request
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function __construct(
        ExtraFiltersConfig $additionalFiltersConfig,
        StoreManagerInterface $storeManager,
        FilterStockService $filterStockService,
        FilterRatingService $filterRatingService,
        FilterNewService $filterNewService,
        FilterOnSaleService $filterOnSaleService,
        RequestInterface $request
    ) {
        $this->additionalFiltersConfig = $additionalFiltersConfig;
        $this->storeManager            = $storeManager;
        $this->filterStockService      = $filterStockService;
        $this->filterRatingService     = $filterRatingService;
        $this->filterNewService        = $filterNewService;
        $this->filterOnSaleService     = $filterOnSaleService;
        $this->storeId                 = $storeManager->getStore()->getId();
        $this->request                 = $request;
    }

    /**
     * Plugin filters product collection with additional filters.
     *
     * @param \Magento\CatalogSearch\Model\Search\IndexBuilder $subject
     * @param \Magento\Framework\DB\Select                     $select
     *
     * @return \Magento\Framework\DB\Select $select
     */
    public function afterBuild($subject, $select)
    {
        if ($this->additionalFiltersConfig->isStockFilterEnabled($this->storeId)) {
            $this->filterStockService->addStockToSelect($select);
        }

        if ($this->additionalFiltersConfig->isRatingFilterEnabled($this->storeId)) {
            $this->filterRatingService->addRatingToSelect($select);
        }

        if ($this->additionalFiltersConfig->isNewFilterEnabled($this->storeId)) {
            $this->filterNewService->addNewToSelect(
                $select,
                $this->storeId,
                $this->request->getParam(ExtraFiltersConfig::NEW_FILTER_FRONT_PARAM)
            );
        }

        if ($this->additionalFiltersConfig->isOnSaleFilterEnabled($this->storeId)) {
            $this->filterOnSaleService->addOnSaleToSelect(
                $select,
                $this->storeId,
                $this->request->getParam(ExtraFiltersConfig::ON_SALE_FILTER_FRONT_PARAM)
            );
        }

        return $select;
    }
}
