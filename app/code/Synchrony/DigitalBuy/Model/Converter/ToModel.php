<?php

namespace Synchrony\DigitalBuy\Model\Converter;

use Synchrony\DigitalBuy\Model\Data\Condition;
use Synchrony\DigitalBuy\Api\Data\RuleInterface;
use Synchrony\DigitalBuy\Model\Data\Rule as RuleDataModel;
use Synchrony\DigitalBuy\Model\Rule;

class ToModel
{
    /**
     * @var \Synchrony\DigitalBuy\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @var \Magento\Framework\Reflection\DataObjectProcessor
     */
    protected $dataObjectProcessor;

    /**
     * @param \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory
     * @param \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
     */
    public function __construct(
        \Synchrony\DigitalBuy\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Reflection\DataObjectProcessor $dataObjectProcessor
    ) {
        $this->ruleFactory = $ruleFactory;
        $this->dataObjectProcessor = $dataObjectProcessor;
    }

    /**
     * @param \Synchrony\DigitalBuy\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapConditions(\Synchrony\DigitalBuy\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $condition = $dataModel->getCondition();
        if ($condition) {
            $array = $this->dataModelToArray($condition);
            $ruleModel->getConditions()->setConditions([])->loadArray($array);
        }

        return $this;
    }

    /**
     * @param \Synchrony\DigitalBuy\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapActionConditions(\Synchrony\DigitalBuy\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $condition = $dataModel->getActionCondition();
        if ($condition) {
            $array = $this->dataModelToArray($condition, 'actions');
            $ruleModel->getActions()->setActions([])->loadArray($array, 'actions');
        }

        return $this;
    }

    /**
     * @param \Synchrony\DigitalBuy\Model\Rule $ruleModel
     * @param RuleDataModel $dataModel
     * @return $this
     */
    protected function mapFields(\Synchrony\DigitalBuy\Model\Rule $ruleModel, RuleDataModel $dataModel)
    {
        $this->mapConditions($ruleModel, $dataModel);
        $this->mapActionConditions($ruleModel, $dataModel);
        return $this;
    }

    /**
     * Convert recursive array into condition data model
     *
     * @param Condition $condition
     * @param string $key
     * @return array
     */
    public function dataModelToArray(Condition $condition, $key = 'conditions')
    {
        $output = [];
        $output['type'] = $condition->getConditionType();
        $output['value'] = $condition->getValue();
        $output['attribute'] = $condition->getAttributeName();
        $output['operator'] = $condition->getOperator();

        if ($condition->getAggregatorType()) {
            $output['aggregator'] = $condition->getAggregatorType();
        }
        if ($condition->getConditions()) {
            $conditions = [];
            foreach ($condition->getConditions() as $subCondition) {
                $conditions[] = $this->dataModelToArray($subCondition, $key);
            }
            $output[$key] = $conditions;
        }
        return $output;
    }

    /**
     * @param RuleDataModel $dataModel
     * @return $this|Rule
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\InputException
     */
    public function toModel(RuleDataModel $dataModel)
    {
        $ruleId = $dataModel->getRuleId();

        if ($ruleId) {
            $ruleModel = $this->ruleFactory->create()->load($ruleId);
            if (!$ruleModel->getId()) {
                throw new \Magento\Framework\Exception\NoSuchEntityException();
            }
        } else {
            $ruleModel = $this->ruleFactory->create();
            $dataModel->setFromDate(
                $this->formattingDate($dataModel->getFromDate())
            );
            $dataModel->setToDate(
                $this->formattingDate($dataModel->getToDate())
            );
        }

        $modelData = $ruleModel->getData();

        $data = $this->dataObjectProcessor->buildOutputDataArray(
            $dataModel,
            \Synchrony\DigitalBuy\Api\Data\RuleInterface::class
        );

        $mergedData = array_merge($modelData, $data);

        $validateResult = $ruleModel->validateData(new \Magento\Framework\DataObject($mergedData));
        if ($validateResult !== true) {
            $text = '';
            /** @var \Magento\Framework\Phrase $errorMessage */
            foreach ($validateResult as $errorMessage) {
                $text .= $errorMessage->getText();
                $text .= '; ';
            }
            throw new \Magento\Framework\Exception\InputException(new \Magento\Framework\Phrase($text));
        }

        $ruleModel->setData($mergedData);

        $this->mapFields($ruleModel, $dataModel);

        return $ruleModel;
    }

    /**
     * Convert date to ISO8601
     *
     * @param string|null $date
     * @return string|null
     */
    private function formattingDate($date)
    {
        if ($date) {
            $fromDate = new \DateTime($date);
            $date = $fromDate->format(\DateTime::ISO8601);
        }

        return $date;
    }
}
