<?php
namespace Mancini\ShippingZone\Model;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Registry;
use Mancini\ShippingZone\Model\ShippingZone\Zipcodes;

/**
 * @method string getZoneName()
 */
class ShippingZone extends AbstractModel
{
    /**
     * Core store config
     *
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;

    /** @var Zipcodes */
    protected $zipcodes;

    /**
     * ShippingZone constructor.
     * @param Context $context
     * @param ScopeConfigInterface $scopeConfig
     * @param Zipcodes $zipcodes
     * @param Registry $registry
     * @param ResourceModel\ShippingZone $resource
     * @param AbstractDb|null $resourceCollection
     * @param array $data
     */
    public function __construct(
        Context $context,
        ScopeConfigInterface $scopeConfig,
        Zipcodes $zipcodes,
        Registry $registry,
        ResourceModel\ShippingZone $resource,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->zipcodes = $zipcodes;
        parent::__construct(
            $context,
            $registry,
            $resource,
            $resourceCollection,
            $data
        );
    }

    /**
    *   Initialize resource model
    *   @return void
    */
    protected function _construct()
    {
        $this->_init('Mancini\ShippingZone\Model\ResourceModel\ShippingZone');
    }
}
