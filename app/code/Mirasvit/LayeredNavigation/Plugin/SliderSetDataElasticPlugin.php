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

use Magento\Framework\Search\Dynamic\DataProviderInterface;
use Magento\Framework\Search\Request\BucketInterface as RequestBucketInterface;
use Magento\Framework\Search\Dynamic\Algorithm\Repository;
use Magento\Framework\Search\Dynamic\EntityStorageFactory;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Layer\FilterList;
use Magento\Eav\Model\Config as EavConfig;
use Mirasvit\LayeredNavigation\Service\SliderService;
use Mirasvit\SearchElastic\Adapter\Aggregation\DynamicBucket;

class SliderSetDataElasticPlugin
{
    /**
     * @var EntityStorageFactory
     */
    private $entityStorageFactory;
    /**
     * @var Repository
     */
    private $algorithmRepository;
    /**
     * @var EavConfig
     */
    private $eavConfig;

    /**
     * SliderSetDataElasticPlugin constructor.
     * @param EavConfig $eavConfig
     * @param Repository $algorithmRepository
     * @param EntityStorageFactory $entityStorageFactory
     */
    public function __construct(
        EavConfig $eavConfig,
        Repository $algorithmRepository,
        EntityStorageFactory $entityStorageFactory
    ) {
        $this->eavConfig = $eavConfig;
        $this->algorithmRepository = $algorithmRepository;
        $this->entityStorageFactory = $entityStorageFactory;
    }

    /**
     * @param \Mirasvit\SearchElastic\Adapter\Aggregation\DynamicBucket $subject
     * @param \Closure $proceed
     * @param RequestBucketInterface $bucket
     * @param array $dimensions
     * @param array $queryResult
     * @param DataProviderInterface $dataProvider
     * @return array
     */
    public function aroundBuild(
        $subject,
        \Closure $proceed,
        RequestBucketInterface $bucket,
        array $dimensions,
        array $queryResult,
        DataProviderInterface $dataProvider
    ) {
        $attribute = $this->eavConfig->getAttribute(Product::ENTITY, $bucket->getField());
        $backendType = $attribute->getBackendType();

        if ($backendType != FilterList::DECIMAL_FILTER) {
            return $proceed($bucket, $dimensions, $bucket, $dataProvider);
        }

        $attributeCode = $attribute->getAttributeCode();

        $minMaxSliderData = false;

        if ($attributeCode == 'price') {
            $minMaxSliderData = [];
            $minMaxSliderData[SliderService::SLIDER_DATA . $attributeCode]
                = $dataProvider->getAggregations($this->getEntityStorage($queryResult));
            $minMaxSliderData[SliderService::SLIDER_DATA . $attributeCode]['value']
                = SliderService::SLIDER_DATA . $attributeCode;
        }

        if ($minMaxSliderData && is_array($minMaxSliderData)) {
            $data = $proceed($bucket, $dimensions, $queryResult, $dataProvider);

            return array_merge($minMaxSliderData, $data);
        }

        return $proceed($bucket, $dimensions, $queryResult, $dataProvider);
    }

    /**
     * @param array $queryResult
     * @return \Magento\Framework\Search\Dynamic\EntityStorage
     */
    private function getEntityStorage(array $queryResult)
    {
        $ids = [];
        foreach ($queryResult['hits']['hits'] as $document) {
            $ids[] = $document['_id'];
        }

        return $this->entityStorageFactory->create($ids);
    }
}
