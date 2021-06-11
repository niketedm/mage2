<?php

namespace Mancini\ShippingZone\Block\Adminhtml\ShippingZone\Tab;

use Exception;
use Magento\Backend\Block\Template\Context;
use Magento\Backend\Block\Widget\Grid;
use Magento\Backend\Block\Widget\Grid\Column;
use Magento\Backend\Block\Widget\Grid\Extended;
use Magento\Backend\Helper\Data;
use Magento\Catalog\Model\ProductFactory;
use Magento\Framework\Registry;
use Mancini\ShippingZone\Model\ShippingZone;
use Mancini\ShippingZone\Model\ShippingZone\ZipcodesFactory;

class Zipcodes extends Extended
{
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;

    /** @var ZipcodesFactory */
    protected $_zipcodesFactory;

    /**
     * @param Context $context
     * @param Data $backendHelper
     * @param ZipcodesFactory $zipcodesFactory
     * @param Registry $coreRegistry
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $backendHelper,
        ZipcodesFactory $zipcodesFactory,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_zipcodesFactory = $zipcodesFactory;
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('shipping_zone_zipcodes');
        $this->setDefaultSort('id');
        $this->setUseAjax(true);
    }

    /**
     * @return ShippingZone
     */
    public function getShippingZone()
    {
        return $this->_coreRegistry->registry('shipping_zone');
    }

    /**
     * @param Column $column
     * @return $this
     */
    protected function _addColumnFilterToCollection($column)
    {
        parent::_addColumnFilterToCollection($column);

        return $this;
    }

    /**
     * @return Grid
     */
    protected function _prepareCollection()
    {
        $shippingZone = $this->getShippingZone();
        $collection = $this->_zipcodesFactory->create()->getCollection()
            ->addFieldToFilter('zone_id', $shippingZone->getId());
        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    /**
     * @return Extended
     * @throws Exception
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'id',
            [
                'header' => __('ID'),
                'sortable' => true,
                'index' => 'id',
                'header_css_class' => 'col-id',
                'column_css_class' => 'col-id'
            ]
        );
        $this->addColumn('zipcode', ['header' => __('Zipcode'), 'index' => 'zipcode']);
        $this->addColumn('city', ['header' => __('City'), 'index' => 'city']);
        $this->addColumn('state', ['header' => __('State'), 'index' => 'state']);

        return parent::_prepareColumns();
    }

    /**
     * @return string
     */
    public function getGridUrl()
    {
        return $this->getUrl('shippingzone/index/zipcodesgrid', ['_current' => true]);
    }
}
