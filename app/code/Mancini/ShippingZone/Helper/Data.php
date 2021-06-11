<?php

namespace Mancini\ShippingZone\Helper;

use Magento\Framework\App\Helper\Context;
use Mancini\ShippingZone\Model\ShippingZone;
use Mancini\ShippingZone\Model\ShippingZone\Zipcodes;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    /** @var ShippingZone */
    protected $shippingZone;

    /** @var Zipcodes */
    protected $zipcodes;

    /**
     * Data constructor.
     * @param Context $context
     * @param ShippingZone $shippingZone
     * @param Zipcodes $zipcodes
     */
    public function __construct(Context $context, ShippingZone $shippingZone, Zipcodes $zipcodes)
    {
        parent::__construct($context);
        $this->shippingZone = $shippingZone;
        $this->zipcodes = $zipcodes;
    }

    /**
     * @param $zipcode
     * @return ShippingZone|null
     */
    public function getShippingZoneByZipcode($zipcode)
    {
        $collection = $this->zipcodes->getCollection()->addFieldToFilter('zipcode', $zipcode);
        if ($collection->getSize() > 0) {
            $item = $collection->getFirstItem();
            return $this->shippingZone->load($item->getZoneId());
            //return $item->getZoneId();
        }

        return null;
    }
}
