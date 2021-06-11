<?php

namespace Mancini\ProductConsole\Model\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Import extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::DEBUG;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/productconsole_import.log';
}
