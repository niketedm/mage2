<?php

namespace Synchrony\DigitalBuy\Api;

/**
 * Synchrony Promotion rule CRUD interface
 *
 * @api
 */
interface RuleRepositoryInterface
{
    /**
     * Save Synchrony promotion rule.
     *
     * @param \Synchrony\DigitalBuy\Api\Data\RuleInterface $rule
     * @return \Synchrony\DigitalBuy\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\InputException If there is a problem with the input
     * @throws \Magento\Framework\Exception\NoSuchEntityException If a rule ID is sent but the rule does not exist
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function save(\Synchrony\DigitalBuy\Api\Data\RuleInterface $rule);

    /**
     * Get rule by ID.
     *
     * @param int $ruleId
     * @return \Synchrony\DigitalBuy\Api\Data\RuleInterface
     * @throws \Magento\Framework\Exception\NoSuchEntityException If $id is not found
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getById($ruleId);

    /**
     * Retrieve prmotion rules that match te specified criteria.
     *
     * @param \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
     * @return \Synchrony\DigitalBuy\Api\Data\RuleSearchResultInterface
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

    /**
     * Delete rule by ID.
     *
     * @param int $ruleId
     * @return bool true on success
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function deleteById($ruleId);
}
