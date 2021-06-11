<?php

namespace Mancini\ShippingZone\Model\ShippingZone;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mancini\ShippingZone\Model\ResourceModel\ShippingZone\Zipcodes as ResourceZipcodes;

/**
 * Class Zipcodes
 * @method getZipcode
 * @method getCity
 * @method getState
 */
class Zipcodes extends AbstractModel
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Registry $registry,
        ResourceZipcodes $resource,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->_scopeConfig = $scopeConfig;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
     * Initialize resource model
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Mancini\ShippingZone\Model\ResourceModel\ShippingZone\Zipcodes');
    }
}
