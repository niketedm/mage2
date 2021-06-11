<?php

namespace Synchrony\DigitalBuy\Model;

use \Synchrony\DigitalBuy\Gateway\Config\AbstractPaymentConfig as Config;

class Logger extends \Magento\Framework\Logger\Monolog
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Logger constructor.
     * @param string $name
     * @param Config $config
     * @param HandlerInterface[] $handlers Optional stack of handlers, the first one in the array is called first, etc
     * @param callable[] $processors Optional array of processors
     */
    public function __construct($name, Config $config, array $handlers = [], array $processors = [])
    {
        parent::__construct($name, $handlers, $processors);
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function debug($message, array $context = [])
    {
        if (!$this->config->getIsDebugOn()) {
            return true;
        }
        return parent::debug($message, $context);
    }
}
