<?php

namespace Mancini\ShippingZone\Model\ResourceModel\ShippingZone\Zipcodes;

use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;

class Collection extends AbstractCollection
{
    /**
     * Constructor
     * Configures  collection
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mancini\ShippingZone\Model\ShippingZone\Zipcodes', 'Mancini\ShippingZone\Model\ResourceModel\ShippingZone\Zipcodes');
    }
}
