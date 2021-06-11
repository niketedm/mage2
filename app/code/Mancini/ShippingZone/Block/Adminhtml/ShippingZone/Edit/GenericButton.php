<?php

namespace Mancini\ShippingZone\Block\Adminhtml\ShippingZone\Edit;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Exception\NoSuchEntityException;
use Mancini\ShippingZone\Model\ShippingZoneFactory;

/**
 * Class GenericButton
 */
class GenericButton
{
    /** @var Context */
    protected $context;

    /** @var ShippingZoneFactory */
    protected $shippingZoneFactory;

    /**
     * @param Context $context
     * @param ShippingZoneFactory $shippingZoneFactory
     */
    public function __construct(
        Context $context,
        ShippingZoneFactory $shippingZoneFactory
    ) {
        $this->context = $context;
        $this->shippingZoneFactory = $shippingZoneFactory;
    }

    /**
     * Return CMS block ID
     *
     * @return int|null
     */
    public function getShippingZoneId()
    {
        try {
            return $this->shippingZoneFactory->create()->load(
                $this->context->getRequest()->getParam('id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
