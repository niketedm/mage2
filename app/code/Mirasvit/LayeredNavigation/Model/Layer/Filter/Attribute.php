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

use Magento\Catalog\Model\Layer;
use Magento\Catalog\Model\Layer\Filter\AbstractFilter;
use Magento\Catalog\Model\Layer\Filter\Item\DataBuilder as ItemDataBuilder;
use Magento\Catalog\Model\Layer\Filter\ItemFactory as FilterItemFactory;
use Magento\Catalog\Model\ResourceModel\Layer\Filter\AttributeFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filter\StripTags as StripTagsFilter;
use Magento\Framework\Stdlib\StringUtils;
use Magento\Search\Model\SearchEngine;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\LayeredNavigation\Api\Data\AttributeConfigInterface;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Model\Config\ConfigTrait;
use Mirasvit\LayeredNavigation\Model\Config\StateBarConfig;
use Mirasvit\LayeredNavigation\Repository\AttributeConfigRepository;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Attribute extends AbstractFilter
{
    use ConfigTrait;

    public static $responseCache = [];

    /**
     * @var array
     */
    protected static $isStateAdded = [];

    private          $filterClearBlockConfig;

    private          $stripTagsFilter;

    private          $attributeConfigRepository;

    private          $searchEngine;

    private          $resource;

    private          $request;

    private          $string;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        AttributeConfigRepository $attributeConfigRepository,
        FilterItemFactory $filterItemFactory,
        StoreManagerInterface $storeManager,
        Layer $layer,
        ItemDataBuilder $itemDataBuilder,
        StripTagsFilter $stripTagsFilter,
        SearchEngine $searchEngine,
        AttributeFactory $filterAttributeFactory,
        RequestInterface $request,
        StringUtils $string,
        StateBarConfig $filterClearBlockConfig,
        array $data = []
    ) {
        parent::__construct(
            $filterItemFactory,
            $storeManager,
            $layer,
            $itemDataBuilder,
            $data
        );

        $this->attributeConfigRepository = $attributeConfigRepository;
        $this->stripTagsFilter           = $stripTagsFilter;
        $this->searchEngine              = $searchEngine;
        $this->resource                  = $filterAttributeFactory->create();
        $this->request                   = $request;
        $this->string                    = $string;
        $this->filterClearBlockConfig    = $filterClearBlockConfig;
    }


    /**
     * Apply attribute option filter to product collection.
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     */
    public function apply(\Magento\Framework\App\RequestInterface $request)
    {
        if (!ConfigTrait::isMultiselectEnabled()) {
            return $this->getDefaultApply($request);
        }

        $filter = $request->getParam($this->_requestVar);

        if (empty($filter)) {
            return $this;
        }
        $options = explode(',', $filter);

        $this->addState(false, $options);
        $this->addProducts($options);

        return $this;
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
        return $this->getCatalogSearchApply($request);
    }

    /**
     * Get data array for building attribute filter items.
     * @return array
     */
    protected function _getItemsData()
    {
        if (!ConfigTrait::isMultiselectEnabled() && ($this->request->getRouteName() == Config::IS_CATALOG_SEARCH)) {
            return $this->getDefaultItemsData();
        }

        $attribute         = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();
        $options           = $attribute->getFrontend()->getSelectOptions();

        $optionsFacetedData = $this->getOptionsFacetedData();

        $this->addItemsToDataBuilder($options, $optionsFacetedData);

        $itemsData = $this->getItemsFromDataBuilder();

        return $itemsData;
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    protected function getDefaultItemsData()
    {
        if ($this->request->getRouteName() == Config::IS_CATALOG_SEARCH) {
            return $this->getCatalogSearchItemsData();
        } else {
            return $this->getCatalogItemsData();
        }
    }

    /**
     * Retrieve resource instance
     * @return \Magento\Catalog\Model\ResourceModel\Layer\Filter\Attribute
     */
    protected function _getResource()
    {
        return $this->resource;
    }

    /**
     * Apply attribute option filter to product collection
     *
     * @param \Magento\Framework\App\RequestInterface $request
     *
     * @return $this
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    private function getCatalogSearchApply(\Magento\Framework\App\RequestInterface $request)
    {
        $attributeValue = $request->getParam($this->_requestVar);
        if (empty($attributeValue) || !is_numeric($attributeValue)) {
            return $this;
        }
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection = $this->getLayer()
            ->getProductCollection();
        $productCollection->addFieldToFilter($attribute->getAttributeCode(), $attributeValue);
        $label = $this->getOptionText($attributeValue);

        $this->addState($label, $attributeValue);

        $this->setItems([]); // set items to disable show filtering

        return $this;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     * @param string|bool $label
     * @param array       $options
     *
     * @return bool
     */
    private function addState($label, $options)
    {
        $state = is_array($options) ? $this->_requestVar . implode('_', $options) : $this->_requestVar . $options;
        if (isset(self::$isStateAdded[$state])) { //avoid double state adding (horizontal filters)
            return true;
        }
        if (is_array($options) && !$label && $this->filterClearBlockConfig->isFilterClearBlockInOneRow()) {
            $labels = [];
            foreach ($options as $option) {
                $labels[] = $this->getOptionText($option);
            }
            $options = (count($options) > 1) ? implode(',', $options) : $option;
            $this->getLayer()->getState()
                ->addFilter($this->_createItem(implode(', ', $labels), $options));
        } elseif (is_array($options) && !$label) {
            foreach ($options as $option) {
                $this->getLayer()->getState()
                    ->addFilter($this->_createItem($this->getOptionText($option), $option));
            }
        } else {
            $this->getLayer()->getState()->addFilter($this->_createItem($label, $options));
        }

        self::$isStateAdded[$state] = true;

        return true;
    }

    /**
     * @param array $options
     * return void
     */
    private function addProducts(array $options)
    {
        $attribute      = $this->getAttributeModel();
        $prodCollection = $this->getLayer()->getProductCollection();
        $prodCollection->addFieldToFilter($attribute->getAttributeCode(), ["in" => $options]);
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    private function getCatalogSearchItemsData()
    {
        $attribute = $this->getAttributeModel();
        /** @var \Magento\CatalogSearch\Model\ResourceModel\Fulltext\Collection $productCollection */
        $productCollection  = $this->getLayer()
            ->getProductCollection();
        $optionsFacetedData = $productCollection->getFacetedData($attribute->getAttributeCode());

        $isAttributeFilterable
            = $this->getAttributeIsFilterable($attribute) === static::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS;

        if (count($optionsFacetedData) === 0 && !$isAttributeFilterable) {
            return $this->itemDataBuilder->build();
        }

        $productSize = $productCollection->getSize();

        $options = $attribute->getFrontend()
            ->getSelectOptions();
        foreach ($options as $option) {
            $this->buildOptionData($option, $isAttributeFilterable, $optionsFacetedData, $productSize);
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * Build option data
     *
     * @param array   $option
     * @param boolean $isAttributeFilterable
     * @param array   $optionsFacetedData
     * @param int     $productSize
     *
     * @return void
     */
    private function buildOptionData($option, $isAttributeFilterable, $optionsFacetedData, $productSize)
    {
        $value = $this->getOptionValue($option);
        if ($value === false) {
            return;
        }
        $count = $this->getOptionCount($value, $optionsFacetedData);
        if ($isAttributeFilterable && (!$this->isOptionReducesResults($count, $productSize) || $count === 0)) {
            return;
        }

        $this->itemDataBuilder->addItemData(
            $this->stripTagsFilter->filter($option['label']),
            $value,
            $count
        );
    }

    /**
     * Retrieve option value if it exists
     *
     * @param array $option
     *
     * @return bool|string
     */
    private function getOptionValue($option)
    {
        if (empty($option['value']) || !is_numeric($option['value'])) {
            return false;
        }

        return $option['value'];
    }

    /**
     * Retrieve count of the options
     *
     * @param int|string $value
     * @param array      $optionsFacetedData
     *
     * @return int
     */
    private function getOptionCount($value, $optionsFacetedData)
    {
        return isset($optionsFacetedData[$value]['count'])
            ? (int)$optionsFacetedData[$value]['count']
            : 0;
    }

    /**
     * Get data array for building category filter items
     * @return array
     */
    private function getCatalogItemsData()
    {
        $attribute         = $this->getAttributeModel();
        $this->_requestVar = $attribute->getAttributeCode();

        $options      = $attribute->getFrontend()->getSelectOptions();
        $optionsCount = $this->_getResource()->getCount($this);
        foreach ($options as $option) {
            if (is_array($option['value'])) {
                continue;
            }
            if ($this->string->strlen($option['value'])) {
                // Check filter type
                if ($this->getAttributeIsFilterable($attribute) == self::ATTRIBUTE_OPTIONS_ONLY_WITH_RESULTS) {
                    if (!empty($optionsCount[$option['value']])) {
                        $this->itemDataBuilder->addItemData(
                            $this->stripTagsFilter->filter($option['label']),
                            $option['value'],
                            $optionsCount[$option['value']]
                        );
                    }
                } else {
                    $this->itemDataBuilder->addItemData(
                        $this->stripTagsFilter->filter($option['label']),
                        $option['value'],
                        isset($optionsCount[$option['value']]) ? $optionsCount[$option['value']] : 0
                    );
                }
            }
        }

        return $this->itemDataBuilder->build();
    }

    /**
     * @return array
     */
    private function getOptionsFacetedData()
    {
        /** @var \Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext\Collection $collection */
        $collection    = $this->getLayer()->getProductCollection();
        $attribute     = $this->getAttributeModel();
        $queryResponse = $this->getAlteredQueryResponse();

        $optionsFacetedData = $collection->getFacetedData($attribute->getAttributeCode(), $queryResponse);

        return $optionsFacetedData;
    }

    /**
     * @return \Magento\Framework\Search\ResponseInterface|null
     */
    private function getAlteredQueryResponse()
    {
        $code     = $this->getAttributeModel()->getAttributeCode();
        $response = null;

        /** @var \Mirasvit\LayeredNavigation\Model\ResourceModel\Fulltext\Collection $collection */
        $collection     = $this->getLayer()->getProductCollection();
        $requestBuilder = clone $collection->getCloneRequestBuilder();

        /** @var \Magento\Framework\Search\Request $queryRequest */
        $queryRequest = $requestBuilder->create();

        /** @var \Magento\Framework\Search\Request\Query\BoolExpression $query */
        $query = $queryRequest->getQuery();

        $mustList = [];
        foreach ($query->getMust() as $must) {
            if ($must->getType() === 'filteredQuery') {
                /** @var \Magento\Framework\Search\Request\Query\Filter $must */
                if ($must->getName() != "{$code}_query") {
                    $mustList[$must->getName()] = $must;
                }
            } else {
                $mustList[$must->getName()] = $must;
            }
        }

        $shouldList = [];
        foreach ($query->getShould() as $should) {
            if ($should->getType() === 'filteredQuery') {
                /** @var \Magento\Framework\Search\Request\Query\Filter $should */
                if ($should->getName() != "{$code}_query") {
                    $shouldList[$should->getName()] = $should;
                }
            } else {
                $shouldList[$should->getName()] = $should;
            }
        }

        $boolQuery = new \Magento\Framework\Search\Request\Query\BoolExpression(
            $query->getName(),
            $query->getBoost(),
            $mustList,
            $shouldList,
            $query->getMustNot()
        );

        $finalRequest = new \Magento\Framework\Search\Request(
            $queryRequest->getName(),
            $queryRequest->getIndex(),
            $boolQuery,
            $queryRequest->getFrom(),
            $queryRequest->getSize(),
            $queryRequest->getDimensions(),
            $queryRequest->getAggregation()
        );

        $hash = $requestBuilder->hash($finalRequest);

        if (!isset(self::$responseCache[$hash])) {
            self::$responseCache[$hash] = $this->searchEngine->search($finalRequest);
        }

        return self::$responseCache[$hash];
    }

    /**
     * @param array $options
     * @param array $facetData
     *
     * @return void
     */
    private function addItemsToDataBuilder($options, $facetData)
    {
        if (!$options) {
            return;
        }

        $attrConfig = $this->getAttributeConfig();

        if ($attrConfig->getOptionsSortBy() === AttributeConfigInterface::OPTION_SORT_BY_LABEL) {
            usort($options, function ($a, $b) {
                return strcmp($a['label'], $b['label']);
            });
        }

        foreach ($options as $option) {
            if (empty($option['value'])) {
                continue;
            }

            if (isset($facetData[$option['value']])) {
                $count = isset($facetData[$option['value']]['count']) ? $facetData[$option['value']]['count'] : 0;

                $this->itemDataBuilder->addItemData(
                    $this->stripTagsFilter->filter($option['label']),
                    $option['value'],
                    $count
                );
            }
        }
    }

    /**
     * @return AttributeConfigInterface
     */
    private function getAttributeConfig()
    {
        $attrCode = $this->getAttributeModel()->getAttributeCode();

        $attrConfig = $this->attributeConfigRepository->getByAttributeCode($attrCode);

        return $attrConfig ? $attrConfig : $this->attributeConfigRepository->create();
    }

    /**
     * Get items data according to attribute settings.
     * @return array
     */
    private function getItemsFromDataBuilder()
    {
        $itemsData = $this->itemDataBuilder->build();

        if (count($itemsData) == 1
            && !$this->isOptionReducesResults(
                $itemsData[0]['count'],
                $this->getLayer()->getProductCollection()->getSize()
            )) {
            $itemsData = [];
        }

        return $itemsData;
    }
}
