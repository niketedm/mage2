<?php

namespace Synchrony\DigitalBuy\Api\Data;

/**
 * @api
 */
interface RuleSearchResultInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * Get promotion rules.
     *
     * @return \Synchrony\DigitalBuy\Api\Data\RuleInterface[]
     */
    public function getItems();

    /**
     * Set prmotion rules .
     *
     * @param \Synchrony\DigitalBuy\Api\Data\RuleInterface[] $items
     * @return $this
     */
    public function setItems(array $items = null);
}
