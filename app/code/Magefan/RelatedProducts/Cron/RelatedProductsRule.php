<?php

namespace Magefan\RelatedProducts\Cron;

class RelatedProductsRule
{

    public function execute()
    {

        $writer = new \Zend\Log\Writer\Stream(BP . '/var/log/cron.log');
        $logger = new \Zend\Log\Logger();
        $logger->addWriter($writer);
        $logger->info("ROHIT CRON RUN");

        return $this;
    }
}
