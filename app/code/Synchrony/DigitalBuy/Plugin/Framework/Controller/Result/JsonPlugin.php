<?php

namespace Synchrony\DigitalBuy\Plugin\Framework\Controller\Result;

use Synchrony\DigitalBuy\Plugin\Variable\Controller\Adminhtml\System\Variable\WysiwygPluginState;
use Synchrony\DigitalBuy\Model\Source\Variables;
use Magento\Framework\Controller\Result\Json as JsonResult;

class JsonPlugin
{
    /**
     * @var WysiwygPluginState
     */
    private $wysiwygPluginState;

    /**
     * @var Variables
     */
    private $paymentVariables;

    /**
     * JsonPlugin constructor.
     * @param WysiwygPluginState $processVariables
     * @param Variables $paymentVariables
     */
    public function __construct(
        WysiwygPluginState $wysiwygPluginState,
        Variables $paymentVariables
    ) {
        $this->wysiwygPluginState = $wysiwygPluginState;
        $this->paymentVariables = $paymentVariables;
    }

    /**
     * Append synchrony payment variables to list if state is set to active
     *
     * @param JsonResult $subject
     * @param $data
     * @param bool $cycleCheck
     * @param array $options
     * @return array|void
     */
    public function beforeSetData(JsonResult $subject, $data, $cycleCheck = false, $options = [])
    {
        if (is_array($data) && $this->wysiwygPluginState->getAppendVaribles()) {
            array_push($data, $this->paymentVariables->toOptionArray(true));
        }
        return [$data, $cycleCheck, $options];
    }
}
