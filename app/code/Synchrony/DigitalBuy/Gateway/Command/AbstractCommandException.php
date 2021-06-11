<?php

namespace Synchrony\DigitalBuy\Gateway\Command;

use Magento\Payment\Gateway\Command\CommandException;

abstract class AbstractCommandException extends CommandException
{
    /**
     * @var string
     */
    private $failCode;

    /**
     * @param $code
     * @return $this
     */
    public function setFailCode($code)
    {
        $this->failCode = $code;
        return $this;
    }

    /**
     * @return string
     */
    public function getFailCode()
    {
        return $this->failCode;
    }
}
