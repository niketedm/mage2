<?php

namespace Mancini\ProductConsole\Model\Logger\Handler;

use Magento\Framework\Logger\Handler\Base;
use Monolog\Logger;

class Cron extends Base
{
    /**
     * Logging level
     * @var int
     */
    protected $loggerType = Logger::INFO;

    /**
     * File name
     * @var string
     */
    protected $fileName = '/var/log/productconsole_corn.log';
}
