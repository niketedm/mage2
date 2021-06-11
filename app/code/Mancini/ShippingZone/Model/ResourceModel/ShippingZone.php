<?php

namespace Mancini\ShippingZone\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class ShippingZone extends AbstractDb
{
    /**
     *   Initialize resource model
     *   Get table name from config
     *
     *   @return void
     */
    protected function _construct()
    {
        $this->_init('shipping_zone', 'id');
    }
}
