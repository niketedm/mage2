<?php

namespace Mancini\ShippingZone\Model\ShippingZone;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Framework\Registry;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Mancini\ShippingZone\Model\ShippingZone;
use Mancini\ShippingZone\Model\ShippingZoneFactory;

/**
 * Class DataProvider
 *
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends AbstractDataProvider
{
    /** @var AbstractCollection */
    protected $collection;

    /** @var array */
    protected $loadedData;

    /** @var Registry */
    protected $registry;

    /** @var RequestInterface */
    protected $request;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var ShippingZoneFactory */
    protected $shippingZoneFactory;

    /**
     * DataProvider constructor.
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param StoreManagerInterface $storeManager
     * @param Registry $registry
     * @param RequestInterface $request
     * @param ShippingZoneFactory $shippingZoneFactory
     * @param array $meta
     * @param array $data
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        StoreManagerInterface $storeManager,
        Registry $registry,
        RequestInterface $request,
        ShippingZoneFactory $shippingZoneFactory,
        array $meta = [],
        array $data = []
    ) {
        $this->registry = $registry;
        $this->storeManager = $storeManager;
        $this->request = $request;
        $this->shippingZoneFactory = $shippingZoneFactory;
        $this->collection = $shippingZoneFactory->create()->getCollection();
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * @return array
     */
    public function getNewData()
    {
        return array(
            'id' => '',
            'zone_name' => '',
            'standard_shipping_cost' => '',
            'premium_shipping_cost' => ''
        );

    }

    /**
     * Get data
     *
     * @return array
     * @throws NoSuchEntityException
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }
        $shippingZone = $this->getCurrentShippingZone();
        if ($shippingZone) {
            $shippingZoneData = $shippingZone->getData();
            $this->loadedData[$shippingZone->getId()] = $shippingZoneData;
        } else {
            $this->loadedData[0] = $this->getNewData();
        }

        return $this->loadedData;
    }

    /**
     * Get current category
     *
     * @return ShippingZone
     * @throws NoSuchEntityException
     */
    public function getCurrentShippingZone()
    {
        $shippingZone = null;
        $requestId = $this->request->getParam($this->requestFieldName);
        if ($requestId) {
            $shippingZone = $this->shippingZoneFactory->create();
            $shippingZone->load($requestId);
            if (!$shippingZone->getId()) {
                throw NoSuchEntityException::singleField('id', $requestId);
            }
        }
        return $shippingZone;
    }
}
