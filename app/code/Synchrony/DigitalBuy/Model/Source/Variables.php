<?php

namespace Synchrony\DigitalBuy\Model\Source;

class Variables implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Array of configuration variables
     *
     * @var array
     */
    private $_configVariables = [];

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->_configVariables = [
            ['value' => 'payment/synchrony_digitalbuy/title', 'label' => __('Payment Method Title')],
            ['value' => 'payment/synchrony_digitalbuy/apply_now_url', 'label' => __('Apply Now URL')],
        ];
    }

    /**
     * Retrieve option array of Synchrony payment variables
     *
     * @param bool $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = [];
        foreach ($this->getData() as $variable) {
            $optionArray[] = [
                'value' => '{{config path="' . $variable['value'] . '"}}',
                'label' => $variable['label'],
            ];
        }
        if ($withGroup && $optionArray) {
            $optionArray = ['label' => __('Synchrony Revolving Payment Variables'), 'value' => $optionArray];
        }
        return $optionArray;
    }

    /**
     * Return available config variables
     *
     * @return array
     */
    public function getData()
    {
        return $this->_configVariables;
    }
}
