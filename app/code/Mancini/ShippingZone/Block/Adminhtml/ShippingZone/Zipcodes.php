<?php

namespace Mancini\ShippingZone\Block\Adminhtml\ShippingZone;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Block\Adminhtml\Category\Tab\Product;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Json\EncoderInterface;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\BlockInterface;
use Mancini\ShippingZone\Model\ShippingZone;

class Zipcodes extends Template
{
    /**
     * Block template
     *
     * @var string
     */
    protected $_template = 'shippingzone/edit/zipcodes.phtml';

    /** @var Product */
    protected $blockGrid;

    /** @var Registry */
    protected $registry;

    /** @var EncoderInterface */
    protected $jsonEncoder;

    /**
     * AssignProducts constructor.
     *
     * @param Context $context
     * @param Registry $registry
     * @param EncoderInterface $jsonEncoder
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        EncoderInterface $jsonEncoder,
        array $data = []
    ) {
        $this->registry = $registry;
        $this->jsonEncoder = $jsonEncoder;
        parent::__construct($context, $data);
    }

    /**
     * Retrieve instance of grid block
     *
     * @return BlockInterface
     * @throws LocalizedException
     */
    public function getBlockGrid()
    {
        if (null === $this->blockGrid) {
            $this->blockGrid = $this->getLayout()->createBlock(
                'Mancini\ShippingZone\Block\Adminhtml\ShippingZone\Tab\Zipcodes',
                'shipping.zone.zipcodes.grid'
            );
        }
        return $this->blockGrid;
    }

    /**
     * Return HTML of grid block
     *
     * @return string
     * @throws LocalizedException
     */
    public function getGridHtml()
    {
        return $this->getBlockGrid()->toHtml();
    }

    /**
     * @return string
     */
    public function getZipcodesJson()
    {
        $zipcodes = $this->getShippingZone()->getZipcodes();
        if (!empty($zipcodes)) {
            return $this->jsonEncoder->encode($zipcodes);
        }
        return '{}';
    }

    /**
     * @return ShippingZone
     */
    public function getShippingZone()
    {
        return $this->registry->registry('shipping_zone');
    }
}
