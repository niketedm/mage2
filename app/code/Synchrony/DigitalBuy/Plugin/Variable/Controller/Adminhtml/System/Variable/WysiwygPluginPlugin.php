<?php

namespace Synchrony\DigitalBuy\Plugin\Variable\Controller\Adminhtml\System\Variable;

use Magento\Variable\Controller\Adminhtml\System\Variable\WysiwygPlugin;

class WysiwygPluginPlugin
{
    /**
     * @var WysiwygPluginState
     */
    private $state;

    /**
     * WysiwygPluginPlugin constructor.
     * @param WysiwygPluginState $processVariables
     */
    public function __construct(
        WysiwygPluginState $processVariables
    ) {
        $this->state = $processVariables;
    }

    /**
     * Activate Singleton state object which allows to process synchrony payment variables
     *
     * @param WysiwygPlugin $subject
     */
    public function beforeExecute(WysiwygPlugin $subject)
    {
        $this->state->setAppendVaribles();
    }

    /**
     * Activate Singleton state object which allows to process synchrony payment variables
     *
     * @param WysiwygPlugin $subject
     */
    public function afterExecute(WysiwygPlugin $subject, $result)
    {
        $this->state->setAppendVaribles(false);
        return $result;
    }
}
