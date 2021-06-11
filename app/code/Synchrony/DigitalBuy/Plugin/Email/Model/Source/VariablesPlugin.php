<?php

namespace Synchrony\DigitalBuy\Plugin\Email\Model\Source;

use Synchrony\DigitalBuy\Model\Source\Variables;
use Synchrony\DigitalBuy\Plugin\Email\Model\Template\FilterState;

class VariablesPlugin
{
    /**
     * @var Variables
     */
    private $paymentVariable;

    /**
     * @var FilterState
     */
    private $filterState;

    /**
     * VariablesPlugin constructor.
     * @param FilterState $filterState
     * @param Variables $paymentVariable
     */
    public function __construct(
        FilterState $filterState,
        Variables $paymentVariable
    ) {
        $this->filterState = $filterState;
        $this->paymentVariable = $paymentVariable;
    }

    /**
     * Add synchrony payment variables to processed by Widget template filters
     *
     * @param Magento\Email\Model\Source\Variables $subject
     * @param array $data
     * @return array
     */
    public function afterGetData(\Magento\Email\Model\Source\Variables $subject, $data)
    {
        if (is_array($data) && $this->filterState->getAppendVaribles()) {
            $data = array_merge($data, $this->paymentVariable->getData());
        }
        return $data;
    }
}
