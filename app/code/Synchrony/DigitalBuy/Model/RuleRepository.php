<?php

namespace Synchrony\DigitalBuy\Model;

use Magento\Framework\Api\Search\FilterGroup;
use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Api\SortOrder;
use Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection;

/**
 * Promotion rule CRUD class
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class RuleRepository implements \Synchrony\DigitalBuy\Api\RuleRepositoryInterface
{
    /**
     * @var \Synchrony\DigitalBuy\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Synchrony\DigitalBuy\Api\Data\RuleInterfaceFactory
     */
    protected $ruleDataFactory;

    /**
     * @var \Synchrony\DigitalBuy\\Api\Data\ConditionInterfaceFactory
     */
    protected $conditionDataFactory;

    /**
     * @var \MSynchrony\DigitalBuy\\Model\Converter\ToDataModel
     */
    protected $toDataModelConverter;

    /**
     * @var \Synchrony\DigitalBuy\\Model\Converter\ToModel
     */
    protected $toModelConverter;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var \Synchrony\DigitalBuy\Api\Data\RuleSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface
     */
    protected $extensionAttributesJoinProcessor;

    /**
     * @var \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory
     */
    protected $ruleCollectionFactory;

    /**
     * @var \Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface
     */
    private $collectionProcessor;

    /**
     * RuleRepository constructor.
     * @param \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory
     * @param \Synchrony\DigitalBuy\Api\Data\RuleInterfaceFactory $ruleDataFactory
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterfaceFactory $conditionDataFactory
     * @param Converter\ToDataModel $toDataModelConverter
     * @param Converter\ToModel $toModelConverter
     * @param \Synchrony\DigitalBuy\Api\Data\RuleSearchResultInterfaceFactory $searchResultFactory
     * @param \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor
     * @param ResourceModel\Rule\CollectionFactory $ruleCollectionFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param CollectionProcessorInterface $collectionProcessor
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory,
        \Synchrony\DigitalBuy\Api\Data\RuleInterfaceFactory $ruleDataFactory,
        \Synchrony\DigitalBuy\Api\Data\ConditionInterfaceFactory $conditionDataFactory,
        \Synchrony\DigitalBuy\Model\Converter\ToDataModel $toDataModelConverter,
        \Synchrony\DigitalBuy\Model\Converter\ToModel $toModelConverter,
        \Synchrony\DigitalBuy\Api\Data\RuleSearchResultInterfaceFactory $searchResultFactory,
        \Magento\Framework\Api\ExtensionAttribute\JoinProcessorInterface $extensionAttributesJoinProcessor,
        \Synchrony\DigitalBuy\Model\ResourceModel\Rule\CollectionFactory $ruleCollectionFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        CollectionProcessorInterface $collectionProcessor
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->conditionDataFactory = $conditionDataFactory;
        $this->toDataModelConverter = $toDataModelConverter;
        $this->toModelConverter = $toModelConverter;
        $this->searchResultFactory = $searchResultFactory;
        $this->extensionAttributesJoinProcessor = $extensionAttributesJoinProcessor;
        $this->ruleCollectionFactory = $ruleCollectionFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->collectionProcessor = $collectionProcessor;
    }

    /**
     * {@inheritdoc}
     */
    public function save(\Synchrony\DigitalBuy\Api\Data\RuleInterface $rule)
    {
        $model = $this->toModelConverter->toModel($rule);
        $model->save();
        $model->load($model->getId());
        $model->getStoreLabels();
        return $this->toDataModelConverter->toDataModel($model);
    }

    /**
     * {@inheritdoc}
     */
    public function getById($id)
    {
        $model = $this->ruleFactory->create()
            ->load($id);

        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }

        $model->getStoreLabels();
        $dataModel = $this->toDataModelConverter->toDataModel($model);
        return $dataModel;
    }

    /**
     * {@inheritdoc}
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var \Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection $collection */
        $collection = $this->ruleCollectionFactory->create();
        $ruleInterfaceName = \Synchrony\DigitalBuy\Api\Data\RuleInterface::class;
        $this->extensionAttributesJoinProcessor->process($collection, $ruleInterfaceName);

        $this->collectionProcessor->process($searchCriteria, $collection);
        $rules = [];
        /** @var \Synchrony\DigitalBuy\Model\Rule $ruleModel */
        foreach ($collection->getItems() as $ruleModel) {
            $ruleModel->load($ruleModel->getId());
            $rules[] = $this->toDataModelConverter->toDataModel($ruleModel);
        }

        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($rules);
        $searchResults->setTotalCount($collection->getSize());
        return $searchResults;
    }

    /**
     * Delete synchrony promotion rule by ID.
     *
     * @param int $id
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($id)
    {
        $model = $this->ruleFactory->create()->load($id);

        if (!$model->getId()) {
            throw new \Magento\Framework\Exception\NoSuchEntityException();
        }
        $model->delete();
        return true;
    }
}
