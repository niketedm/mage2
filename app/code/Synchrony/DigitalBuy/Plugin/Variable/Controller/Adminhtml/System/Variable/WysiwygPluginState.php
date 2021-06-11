<?php

namespace Synchrony\DigitalBuy\Plugin\Variable\Controller\Adminhtml\System\Variable;

class WysiwygPluginState
{
    /**
     * @var bool
     */
    private $appendVariables = false;

    /**
     * Activate state to start Synchrony payment variable processing
     *
     * @param bool $state
     */
    public function setAppendVaribles($state = true)
    {
        $this->appendVariables = (bool) $state;
    }

    /**
     * Get current state
     *
     * @return bool
     */
    public function getAppendVaribles()
    {
        return $this->appendVariables;
    }
}
