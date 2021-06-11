<?php

namespace Synchrony\DigitalBuy\Model\Converter;

use Synchrony\DigitalBuy\Api\Data\RuleExtensionFactory;
use Synchrony\DigitalBuy\Api\Data\RuleExtensionInterface;
use Synchrony\DigitalBuy\Model\Data\Condition;
use Synchrony\DigitalBuy\Api\Data\RuleInterface;
use Synchrony\DigitalBuy\Model\Data\Rule as RuleDataModel;
use Synchrony\DigitalBuy\Model\Rule;
use Magento\Framework\Serialize\Serializer\Json;

class ToDataModel
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
     * @var \Synchrony\DigitalBuy\Api\Data\ConditionInterfaceFactory
     */
    protected $conditionDataFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @var Json $serializer
     */
    private $serializer;

    /**
     * @var RuleExtensionFactory
     */
    private $extensionFactory;

    /**
     * @param \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory
     * @param \Synchrony\DigitalBuy\Api\Data\RuleInterfaceFactory $ruleDataFactory
     * @param \Synchrony\DigitalBuy\Api\Data\ConditionInterfaceFactory $conditionDataFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     * @param Json $serializer Optional parameter for backward compatibility
     * @param RuleExtensionFactory|null $extensionFactory
     */
    public function __construct(
        \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory,
        \Synchrony\DigitalBuy\Api\Data\RuleInterfaceFactory $ruleDataFactory,
        \Synchrony\DigitalBuy\Api\Data\ConditionInterfaceFactory $conditionDataFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor,
        Json $serializer = null,
        RuleExtensionFactory $extensionFactory = null
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->ruleDataFactory = $ruleDataFactory;
        $this->conditionDataFactory = $conditionDataFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
        $this->serializer = $serializer ?: \Magento\Framework\App\ObjectManager::getInstance()->get(Json::class);
        $this->extensionFactory = $extensionFactory ?:
            \Magento\Framework\App\ObjectManager::getInstance()->get(RuleExtensionFactory::class);
    }

    /**
     * Converts Promotion Rule model to Promotion Rule DTO
     *
     * @param Rule $ruleModel
     * @return RuleDataModel
     */
    public function toDataModel(Rule $ruleModel)
    {
        $modelData = $ruleModel->getData();
        $modelData = $this->convertExtensionAttributesToObject($modelData);

        /** @var \Synchrony\DigitalBuy\Model\Data\Rule $dataModel */
        $dataModel = $this->ruleDataFactory->create(['data' => $modelData]);

        $this->mapFields($dataModel, $ruleModel);

        return $dataModel;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapConditions(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $conditionSerialized = $ruleModel->getConditionsSerialized();
        if ($conditionSerialized) {
            $conditionArray = $this->serializer->unserialize($conditionSerialized);
            $conditionDataModel = $this->arrayToConditionDataModel($conditionArray);
            $dataModel->setCondition($conditionDataModel);
        } else {
            $dataModel->setCondition(null);
        }
        return $this;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapActionConditions(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $actionConditionSerialized = $ruleModel->getActionsSerialized();
        if ($actionConditionSerialized) {
            $actionConditionArray = $this->serializer->unserialize($actionConditionSerialized);
            $actionConditionDataModel = $this->arrayToConditionDataModel($actionConditionArray);
            $dataModel->setActionCondition($actionConditionDataModel);
        } else {
            $dataModel->setActionCondition(null);
        }
        return $this;
    }

    /**
     * Convert extension attributes of model to object if it is an array
     *
     * @param array $data
     * @return array
     */
    private function convertExtensionAttributesToObject(array $data)
    {
        if (isset($data['extension_attributes']) && is_array($data['extension_attributes'])) {
            /** @var RuleExtensionInterface $attributes */
            $data['extension_attributes'] = $this->extensionFactory->create(['data' => $data['extension_attributes']]);
        }
        return $data;
    }

    /**
     * @param RuleDataModel $dataModel
     * @param Rule $ruleModel
     * @return $this
     */
    protected function mapFields(RuleDataModel $dataModel, Rule $ruleModel)
    {
        $this->mapConditions($dataModel, $ruleModel);
        $this->mapActionConditions($dataModel, $ruleModel);
        return $this;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param array $input
     * @return Condition
     */
    public function arrayToConditionDataModel(array $input)
    {
        /** @var \Synchrony\DigitalBuy\Model\Data\Condition $conditionDataModel */
        $conditionDataModel = $this->conditionDataFactory->create();
        foreach ($input as $key => $value) {
            switch ($key) {
                case 'type':
                    $conditionDataModel->setConditionType($value);
                    break;
                case 'attribute':
                    $conditionDataModel->setAttributeName($value);
                    break;
                case 'operator':
                    $conditionDataModel->setOperator($value);
                    break;
                case 'value':
                    $conditionDataModel->setValue($value);
                    break;
                case 'aggregator':
                    $conditionDataModel->setAggregatorType($value);
                    break;
                case 'conditions':
                    $conditions = [];
                    foreach ($value as $condition) {
                        $conditions[] = $this->arrayToConditionDataModel($condition);
                    }
                    $conditionDataModel->setConditions($conditions);
                    break;
                default:
            }
        }
        return $conditionDataModel;
    }
}
