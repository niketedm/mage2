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



namespace Mirasvit\LayeredNavigation\Service;

use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Search\Adapter\Mysql\TemporaryStorageFactory;
use Mirasvit\Core\Service\CompatibilityService;
use Mirasvit\LayeredNavigation\Model\Config;

/**
 * @SuppressWarnings(PHPMD)
 */
class FilterDataService
{
    const BUCKET = '_bucket';

    private $termBucket;

    private $dataProvider;

    private $dynamicBucket;

    private $engine;

    /**
     * @var ObjectManagerInterface
     */
    private $objectManager;

    /**
     * @var ModuleManager
     */
    private $moduleManager;

    /**
     * @var TemporaryStorageFactory
     */
    private $temporaryStorageFactory;

    private $sliderService;

    private $config;

    public function __construct(
        TemporaryStorageFactory $temporaryStorageFactory,
        ModuleManager $moduleManager,
        ObjectManagerInterface $objectManager,
        SliderService $sliderService,
        Config $config
    ) {
        $this->temporaryStorageFactory = $temporaryStorageFactory;
        $this->moduleManager           = $moduleManager;
        $this->objectManager           = $objectManager;
        $this->sliderService           = $sliderService;
        $this->config                  = $config;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string                                  $attributeCode
     *
     * @return array
     * @throws \Exception
     */
    public function getFilterBucketData($request, $attributeCode)
    {
        $is24 = CompatibilityService::is24();
        $mstElasticEnabled = $this->moduleManager->isEnabled('Mirasvit_SearchElastic');
        $engine = false;

        if (class_exists('Mirasvit\Search\Model\Config')) {
            $engine = $this->objectManager->create('Mirasvit\Search\Model\Config')->getEngine();
        } elseif (class_exists('Mirasvit\Search\Model\ConfigProvider')) {
            $engine = $this->objectManager->create('Mirasvit\Search\Model\ConfigProvider')->getEngine();
        }

        if ($is24) {
            if ($engine == 'mysql2') {
                return $this->getDefaultBucket($request, $attributeCode, $is24);
            } elseif ($engine == 'sphinx') {
                return $this->getSphinxBucket($request, $attributeCode);
            }

            return $this->getBucket($request, $attributeCode);
        } else {
            if ($mstElasticEnabled && $engine == 'elastic' && $request->getName() != 'catalog_view_container') {
                return $this->getElasticBucket($request, $attributeCode);
            }

            return $this->getDefaultBucket($request, $attributeCode, $is24);
        }
    }

    /**
     * @param \Magento\Framework\Search\Request $request
     * @param string                            $attributeCode
     *
     * @return array
     */
    protected function getBucket($request, $attributeCode)
    {
        /** @var \Magento\Elasticsearch\SearchAdapter\ConnectionManager $connectionManager */
        $connectionManager = $this->objectManager->create(\Magento\Elasticsearch\SearchAdapter\ConnectionManager::class);

        if ($this->config->getSearchEngine() === 'elasticsearch5') {
            /** @var \Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper $mapper */
            $mapper = $this->objectManager->create(\Magento\Elasticsearch\Elasticsearch5\SearchAdapter\Mapper::class);
        } else {
            /** @var \Magento\Elasticsearch7\SearchAdapter\Mapper $mapper */
            $mapper = $this->objectManager->create(\Magento\Elasticsearch7\SearchAdapter\Mapper::class);
        }

        /** @var \Magento\Framework\Search\Dynamic\DataProviderInterface $mapper */
        $dataProvider = $this->objectManager->create(\Magento\Framework\Search\Dynamic\DataProviderInterface::class);

        $client      = $connectionManager->getConnection();
        $rawResponse = $client->query($mapper->buildQuery($request));

        $currentBucket = $this->getCurrentBucket($request->getAggregation(), $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        /** @var \Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Dynamic $dynamicBucket */
        $dynamicBucket = $this->objectManager->create(\Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Dynamic::class);
        /** @var \Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Term $termBucket */
        $termBucket = $this->objectManager->create(\Magento\Elasticsearch\SearchAdapter\Aggregation\Builder\Term::class);

        if ($currentBucket->getType() == 'dynamicBucket') {
            $responseBucket           = $dynamicBucket->build(
                $currentBucket,
                $request->getDimensions(),
                $rawResponse,
                $dataProvider
            );
            $dataKey                  = $this->sliderService->getSliderDataKey($attributeCode);
            $responseBucket[$dataKey] = [
                'min'   => $rawResponse['aggregations'][$currentBucket->getName()]['min'],
                'max'   => $rawResponse['aggregations'][$currentBucket->getName()]['max'],
                'count' => $rawResponse['aggregations'][$currentBucket->getName()]['count'],
            ];
        } elseif ($currentBucket->getType() == 'termBucket') {
            $responseBucket = $termBucket->build(
                $currentBucket,
                $request->getDimensions(),
                $rawResponse,
                $dataProvider
            );
        } else {
            throw new \Exception("Bucket type not implemented.");
        }

        return $responseBucket;
    }

    /**
     * @param \Magento\Framework\Search\Request $request
     * @param string                            $attributeCode
     * @param bool                              $is24
     *
     * @return array
     */
    protected function getDefaultBucket($request, $attributeCode, $is24) {
        if ($is24) {
            $mapper = $this->objectManager->create(\Mirasvit\SearchMysql\SearchAdapter\Mapper::class);
            $dataProviderContainer = $this->objectManager->create(\Mirasvit\SearchMysql\SearchAdapter\Aggregation\DataProviderContainer::class);
            $aggregationContainer = $this->objectManager->create(\Mirasvit\SearchMysql\SearchAdapter\Aggregation\Builder\Container::class);
        } else {
            $mapper = $this->objectManager->create(\Magento\Framework\Search\Adapter\Mysql\Mapper::class);
            $dataProviderContainer = $this->objectManager->create(\Magento\Framework\Search\Adapter\Mysql\Aggregation\DataProviderContainer::class);
            $aggregationContainer = $this->objectManager->create(\Magento\Framework\Search\Adapter\Mysql\Aggregation\Builder\Container::class);
        }

        $query = $mapper->buildQuery($request);

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table            = $temporaryStorage->storeDocumentsFromSelect($query);
        $dataProvider     = $dataProviderContainer->get($request->getIndex());

        $bucketAggregation = $request->getAggregation();
        $currentBucket     = $this->getCurrentBucket($bucketAggregation, $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        $aggregationBuilder = $aggregationContainer->get($currentBucket->getType());
        $responseBucket = $aggregationBuilder->build(
            $dataProvider,
            $request->getDimensions(),
            $currentBucket,
            $table
        );

        return $responseBucket;
    }

    /**
     * @param \Magento\Framework\Search\Request $request
     * @param string                            $attributeCode
     *
     * @return array
     */
    protected function getSphinxBucket($request, $attributeCode)
    {
        $mapper = $this->objectManager->create(\Mirasvit\SearchSphinx\SearchAdapter\MapperQL::class);
        $dataProviderContainer = $this->objectManager->create(\Mirasvit\SearchMysql\SearchAdapter\Aggregation\DataProviderContainer::class);
        $aggregationContainer = $this->objectManager->create(\Mirasvit\SearchMysql\SearchAdapter\Aggregation\Builder\Container::class);
        $documents = [];

        try {
            $pairs = $this->mapper->buildQuery($request);
        } catch (\Exception $e) {
            $pairs = [];
        }

        foreach ($pairs as $id => $score) {
            $documents[] = [
                '_id'    => $id,
                '_score' => $score,
            ];
        }

        $temporaryStorage = $this->temporaryStorageFactory->create();
        $table = $temporaryStorage->storeApiDocuments($documents);
        $dataProvider = $dataProviderContainer->get($request->getIndex());
        $bucketAggregation = $request->getAggregation();
        $currentBucket     = $this->getCurrentBucket($bucketAggregation, $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        $aggregationBuilder = $aggregationContainer->get($currentBucket->getType());
        $responseBucket = $aggregationBuilder->build(
            $dataProvider,
            $request->getDimensions(),
            $currentBucket,
            $table
        );

        return $responseBucket;
    }

    /**
     * @param array  $bucketAggregation
     * @param string $attributeCode
     *
     * @return \Magento\Framework\Search\Request\Aggregation\TermBucket
     */
    protected function getCurrentBucket($bucketAggregation, $attributeCode)
    {
        $attributeCode = $attributeCode . self::BUCKET;
        $currentBucket = false;
        foreach ($bucketAggregation as $requestBucket) {
            if ($requestBucket->getName() == $attributeCode) {
                $currentBucket = $requestBucket;
                break;
            }
        }

        return $currentBucket;
    }

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @param string                                  $attributeCode
     *
     * @return array
     */
    protected function getElasticBucket($request, $attributeCode)
    {
        $query               = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Mapper')->buildQuery($request);
        $this->dataProvider  = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\DataProvider');
        $this->dynamicBucket = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Aggregation\DynamicBucket');
        $this->termBucket    = $this->objectManager->create('Mirasvit\SearchElastic\Adapter\Aggregation\TermBucket');
        $this->engine        = $this->objectManager->create('Mirasvit\SearchElastic\Model\Engine');
        $client              = $this->engine->getClient();
        $response            = $client->search($query);

        if (is_array($response)) {
            $bucketAggregation = $request->getAggregation();
        } else {
            $bucketAggregation = $response->getAggregation();
        }

        $currentBucket     = $this->getCurrentBucket($bucketAggregation, $attributeCode);

        if (!$currentBucket) {
            return [];
        }

        if ($currentBucket->getType() == 'dynamicBucket') {
            $responseBucket = $this->dynamicBucket->build(
                $currentBucket,
                $request->getDimensions(),
                $response,
                $this->dataProvider
            );
        } elseif ($currentBucket->getType() == 'termBucket') {
            $responseBucket = $this->termBucket->build(
                $currentBucket,
                $response
            );
        } else {
            throw new \Exception("Bucket type not implemented.");
        }

        return $responseBucket;
    }
}
