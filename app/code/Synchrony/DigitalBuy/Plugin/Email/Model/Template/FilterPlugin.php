<?php

namespace Synchrony\DigitalBuy\Plugin\Email\Model\Template;

use Magento\Email\Model\Template\Filter;

class FilterPlugin
{
    /**
     * @var FilterState
     */
    private $state;

    /**
     * FilterPlugin constructor.
     * @param FilterState $processVariables
     */
    public function __construct(
        FilterState $processVariables
    ) {
        $this->state = $processVariables;
    }

    /**
     * Activate Singleton state object which allows to process synchrony payment variables
     *
     * @param Filter $subject
     * @param string[] $construction
     */
    public function beforeConfigDirective(Filter $subject, $construction)
    {
        $this->state->setAppendVaribles();
    }

    /**
     * Deactivate Singleton state object which allows to process synchrony payment variables
     *
     * @param Filter $subject
     * @param string $result
     */
    public function afterConfigDirective(Filter $subject, $result)
    {
        $this->state->setAppendVaribles(false);
        return $result;
    }
}
