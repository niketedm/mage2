<?php

namespace Mancini\ShippingZone\Model\ResourceModel\ShippingZone;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Zipcodes extends AbstractDb
{
    /**
     *  Initialize resource model
     *  Get table name from config
     *
     *  @return void
     */
    protected function _construct()
    {
        $this->_init('shipping_zone_zipcodes', 'id');
    }
}
