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



namespace Mirasvit\LayeredNavigation\Model\Layer\Filter;

use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\DataProvider\CategoryFactory;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory;
use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Escaper;
use Magento\Framework\ObjectManagerInterface;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;
use Mirasvit\LayeredNavigation\Model\Layer\Filter\Category\CategoryDataBuilder;
use Mirasvit\LayeredNavigation\Service\FilterDataService;
use Magento\Catalog\Model\Product\Visibility;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Category extends AbstractFilter
{
    use ConfigTrait;

    const ATTRIBUTE                   = 'category_ids';
    const STORE                       = 'store_id';
    const CATEGORY                    = 'category';
    const CATEGORY_PAGE               = 'catalog_category_view';
    const BRAND_PAGE                  = 'brand_brand_view';
    const ALL_PRODUCTS_PAGE           = 'all_products_page_index_index';
    const CATEGORY_SECOND_WAY_ACTIONS = [self::BRAND_PAGE, self::ALL_PRODUCTS_PAGE];

    private $objectManager;

    private $request;

    private $layer;

    private $filterDataService;

    private $escaper;

    private $dataProvider;

    private $categoryRepository;

    private $storeManager;

    private $filterClearBlockConfig;

    private $storeId;

    /**
     * @var array
     */
    protected static $isStateAdded = [];

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        DataBuilder $itemDataBuilder,
        Escaper $escaper,
        CategoryFactory $categoryDataProviderFactory,
        CategoryRepositoryInterface $categoryRepository,
        LayerResolver $layerResolver,
        RequestInterface $request,
        FilterDataService $filterDataService,
        StateBarConfig $filterClearBlockConfig,
        ObjectManagerInterface $objectManager,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );
        $this->escaper                = $escaper;
        $this->_requestVar            = 'cat';
        $this->dataProvider           = $categoryDataProviderFactory->create(['layer' => $this->getLayer()]);
        $this->categoryRepository     = $categoryRepository;
        $this->layer                  = $layerResolver->get();
        $this->storeManager           = $storeManager;
        $this->request                = $request;
        $this->filterDataService      = $filterDataService;
        $this->filterClearBlockConfig = $filterClearBlockConfig;
        $this->storeId                = $storeManager->getStore()->getId();
        $this->objectManager          = $objectManager;
    }


    /**
     * Apply category filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return  $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!ConfigTrait::isMultiselectEnabled()) {
            return $this->getDefaultApply($request);
        }
        $categoryId = $this->request->getParam($this->getRequestVar()) ? : $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }
        $categoryIds = explode(',', $categoryId);
        $categoryIds = array_unique($categoryIds);
        $categoryIds = array_map('intval', $categoryIds); //must be int
        $categoryIds = array_diff($categoryIds, ['', 0, false, null]); //don't use incorrect data
        /** @var \Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()->getProductCollection();

        if ($request->getParam('id') != $categoryId) {
            $productCollection->addCategoryMultiFilter($categoryIds);

            $category = $this->getLayer()->getCurrentCategory();
            /** @var \Magento\Catalog\Model\ResourceModel\AbstractCollection $collection */
            $collection = $category->getCollection();
            $child      = $collection
                ->addFieldToFilter($category->getIdFieldName(), $categoryIds)
                ->addAttributeToSelect('name');
            $this->addState(false, $categoryIds, $child);
        }

        return $this;
    }

    /**
     * Add data to state
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     *
     * @param string|bool                                                  $categoryName
     * @param array<int, int>                                              $categoryId
     * @param bool|\Magento\Catalog\Model\ResourceModel\AbstractCollection $child
     *
     * @return bool
     */
    protected function addState($categoryName, $categoryId, $child = false)
    {
        $state = is_array($categoryId)
            ? $this->_requestVar . implode('_', $categoryId) : $this->_requestVar . $categoryId;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }

        if (is_array($categoryId) && $child && $this->filterClearBlockConfig->isFilterClearBlockInOneRow()) {
            $labels = [];
            foreach ($categoryId as $categoryIdValue) {
                if ($currentCategory = $child->getItemById($categoryIdValue)) {
                    $labels[] = $currentCategory->getName();
                }
            }
            $this->getLayer()->getState()->addFilter(
                $this->_createItem(
                    implode(', ', $labels),
                    $categoryId
                )
            );
        } elseif (is_array($categoryId) && $child) {
            foreach ($categoryId as $categoryIdValue) {
                if ($currentCategory = $child->getItemById($categoryIdValue)) {
                    $this->getLayer()->getState()->addFilter(
                        $this->_createItem(
                            $currentCategory->getName(),
                            $categoryIdValue
                        )
                    );
                }
            }
        } else {
            $this->getLayer()->getState()->addFilter(
                $this->_createItem(
                    $categoryName,
                    $categoryId
                )
            );
        }

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * Get filter name
     * @return string
     */
    public function getName()
    {
        return __('Category');
    }

    /**
     * {@inheritDoc}
     */
    protected function _initItems()
    {
        $facetedData = $this->getFacetedData();
        $category    = $this->dataProvider->getCategory();

        $items = [];

        if ($category->getIsActive()) {
            $categoryData = $this->getPreparedCategoryData($facetedData);

            foreach ($categoryData as $data) {
                $item = $this->_createItem($data['category_name'], $data['category_id'], $data['count']);
                $item->addData([
                    'category_id' => $data['category_id'],
                    'parent_id'   => $data['parent_id'],
                    'level'       => $data['level'],
                ]);

                $items[] = $item;
            }
        }

        if (count($items) == 1) {
            $collectionSize = $this->getLayer()->getProductCollection()->getSize();
            if (!$this->isOptionReducesResults($items[0]['count'], $collectionSize)) {
                $items = [];
            }
        }

        $this->_items = $items;

        return $this;
    }

    /**
     * Get prepared category data to build category filters
     * for following actions 'brand_brand_view', 'all_products_page_index_index'
     *
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @SuppressWarnings(PHPMD.NPathComplexity)
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     * @param array $facetedData
     *
     * @return array|null
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    protected function getPreparedCategoryData($facetedData)
    {
        if (!$facetedData) {
            return [];
        }

        $flatCategoryData  = [];
        $minLevel          = 1000;
        $parentIds         = [];
        $currentCategoryId = $this->layer->getCurrentCategory()->getId();

        foreach ($facetedData as $categoryId => $optionsFaceted) {
            $category = $this->categoryRepository->get($categoryId, $this->storeId);

            if (!$category || !$category->getIsActive()) {
                continue;
            }

            if (!isset($facetedData[$category->getId()])) {
                continue;
            }

            /** @var CategoryInterface $parentCategory */
            $parentCategory = $category->getParentCategory();
            $parentId       = $parentCategory->getId();

            if (!array_key_exists($parentId, $facetedData)
                && $parentCategory->getLevel() > 1 && $parentCategory->getIsActive()
                && $currentCategoryId != $parentId) {
                $minLevel = ($minLevel > $parentCategory->getLevel()) ? $parentCategory->getLevel() : $minLevel;
                $parentIds[] = sprintf("%d.%d", $parentCategory->getLevel(), $parentCategory->getParentCategory()->getId());
                $flatCategoryData[sprintf("%d.%d", $parentCategory->getLevel(), $parentId)] = [
                    'category_name' => $this->escaper->escapeHtml($parentCategory->getName()),
                    'category_id'   => $parentCategory->getId(),
                    'parent_id'     => sprintf("%d.%d", $parentCategory->getLevel(), $parentCategory->getParentCategory()->getId()),
                    'count'         => 0,
                    'level'         => $parentCategory->getLevel(),
                ];
            }

            $minLevel                                                               = ($minLevel > $category->getLevel()) ? $category->getLevel() : $minLevel;
            $parentIds[]                                                            = sprintf("%d.%d", $category->getLevel() - 1, $parentId);
            $flatCategoryData[sprintf("%d.%d", $category->getLevel(), $categoryId)] = [
                'category_name' => $this->escaper->escapeHtml($category->getName()),
                'category_id'   => $categoryId,
                'parent_id'     => sprintf("%d.%d", $category->getLevel() - 1, $parentId),
                'count'         => $facetedData[$categoryId]['count'],
                'level'         => $category->getLevel(),
            ];
        }

        $categoryData = $flatCategoryData;

        if ($currentCategoryId) {
            foreach ($categoryData as $key => $category) {
                $categoryData[$key]['level'] -= $this->layer->getCurrentCategory()->getLevel();
                if ($categoryData[$key]['level'] == 0) {
                    unset($categoryData[$key]);
                    continue;
                } else {
                    $categoryData[$key]['level'] -= 1;
                }
            }
        } else {
            foreach ($categoryData as $key => $category) {
                $categoryData[$key]['parent_id'] = explode('.', $category['parent_id'])[1];
            }
        }

        $fullActionName       = $this->request->getFullActionName();
        $categoryDataPrepared = null;
        if ($this->storeManager->getStore()->getRootCategoryId() == $currentCategoryId) {
            $categoryDataPrepared = $categoryData;
        } else {
            if ($fullActionName === self::BRAND_PAGE && CompatibilityService::hasModule('Mirasvit_Brand')) {
                if ($this->objectManager->get(\Mirasvit\Brand\Model\Config\GeneralConfig::class)->isShowAllCategories()) {
                    $categoryDataPrepared = $categoryData;
                } else {
                    $categoryDataPrepared = [];
                }
            }

            if ($fullActionName === self::ALL_PRODUCTS_PAGE && CompatibilityService::hasModule('Mirasvit_AllProducts')) {
                if ($this->objectManager->get(\Mirasvit\AllProducts\Config\Config::class)->isShowAllCategories()) {
                    $categoryDataPrepared = $categoryData;
                } else {
                    $categoryDataPrepared = [];
                }
            }

            if (ConfigTrait::isShowNestedCategories()) {
                $categoryDataPrepared = $categoryData;
            }

            if ($categoryDataPrepared === null) {
                $categoryDataPrepared = array_map(function ($category) {
                    if ($category['level'] == 0) {
                        return $category;
                    }
                }, $categoryData);
                $categoryDataPrepared = array_filter($categoryDataPrepared);
            }
        }

        return $categoryDataPrepared;
    }

    /**
     * @param string     $idField
     * @param string     $parentField
     * @param array      $flatCategoryData
     * @param string|int $parentID
     * @param array      $result
     * @param int        $depth
     *
     * @return array
     */
    private function sortCategories($idField, $parentField, $flatCategoryData, $parentID = 0, &$result = [], &$depth = 0)
    {
        foreach ($flatCategoryData as $key => $categoryData) {
            if ($categoryData[$parentField] == $parentID) {
                $categoryData['depth'] = $depth;
                array_push($result, $categoryData);
                unset($flatCategoryData[$key]);
                $oldParent = $parentID;
                $depth++;
                $parentID = sprintf("%d.%d", $depth+1, $categoryData[$idField]);
                $this->sortCategories($idField, $parentField, $flatCategoryData, $parentID, $result, $depth);
                $parentID = $oldParent;
                $depth--;
            }
        }

        return $result;
    }

    /**
     * @return array
     */
    protected function getFacetedData()
    {
        $productCollection = $this->getLayer()->getProductCollection();

        $startCategoryForCountBucket = $this->layer->getCurrentCategory();

        /** @var \Mirasvit\LayeredNavigation\Model\Request\Builder $requestBuilder */
        $requestBuilder = clone $productCollection->getCloneRequestBuilder();
        $requestBuilder->removePlaceholder(self::ATTRIBUTE);
        $requestBuilder->removePlaceholder(self::STORE);
        $requestBuilder->bind(self::STORE, $this->getStoreId());
        $requestBuilder->bind(self::ATTRIBUTE, $startCategoryForCountBucket->getId());

        $productListVisibilityFilter = (int) Visibility::VISIBILITY_IN_CATALOG;
        if ($requestBuilder->hasPlaceholder('search_term')) {
            $productListVisibilityFilter = (int) Visibility::VISIBILITY_IN_SEARCH;
        }

        if (is_numeric($productListVisibilityFilter)) {
            $productListVisibility[] = $productListVisibilityFilter;
            $productListVisibility[] = (int) Visibility::VISIBILITY_BOTH;
            $requestBuilder->bind('visibility', $productListVisibility);
        }

        $searchRequest = $requestBuilder->create();

        return $this->filterDataService->getFilterBucketData($searchRequest, self::CATEGORY);
    }

    /**
     * Apply category filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return  $this
     */
    protected function getDefaultApply($request)
    {
        if ($request->getRouteName() == Config::IS_CATALOG_SEARCH) {
            return $this->getCatalogSearchApply($request);
        } else {
            return $this->getCatalogApply($request);
        }
    }

    /**
     * Apply category filter to layer
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return  $this
     */
    private function getCatalogApply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = (int)$request->getParam($this->getRequestVar());
        if (!$categoryId) {
            return $this;
        }

        $this->dataProvider->setCategoryId($categoryId);

        if ($this->dataProvider->isValid()) {
            $category = $this->dataProvider->getCategory();
            $this->getLayer()->getProductCollection()->addCategoryFilter($category);
            $this->addState($category->getName(), $categoryId);
        }

        return $this;
    }

    /**
     * Apply category filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return  $this
     */
    private function getCatalogSearchApply(\Magento\Framework\App\RequestInterface $request)
    {
        $categoryId = $request->getParam($this->_requestVar) ? : $request->getParam('id');
        if (empty($categoryId)) {
            return $this;
        }

        $this->dataProvider->setCategoryId($categoryId);

        $category = $this->dataProvider->getCategory();

        $this->getLayer()->getProductCollection()->addCategoryFilter($category);

        if ($request->getParam('id') != $category->getId() && $this->dataProvider->isValid()) {
            $this->addState($category->getName(), $categoryId);
        }

        return $this;
    }
}
