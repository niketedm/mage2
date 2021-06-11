<?php

namespace Mancini\ShippingZone\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;
use Magento\Framework\Registry;
use Mancini\ShippingZone\Model\ShippingZone;

class Edit extends Container
{
    protected $_coreRegistry;

    public function __construct(
        Context $context,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        parent::__construct($context, $data);
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->remove('reset');
        $this->buttonList->remove('delete');
        $this->buttonList->update('save', 'label', __('Import'));
        $this->buttonList->update(
            'back',
            'onclick',
            sprintf("location.href = '%s';", $this->getBackUrl())
        );
        $shippingZone = $this->getShippingZone();
        if ($shippingZone->getId()) {
            $this->buttonList->add(
                'download-zipcodes',
                [
                    'label' => __('Download Zipcodes'),
                    'on_click' => sprintf(
                        "location.href = '%s';",
                        $this->getUrl('shippingzone/import/downloadzipcodes', ['id' => $shippingZone->getId()])
                    ),
                    'class' => 'action-secondary download',
                ],
                -100
            );
        }

        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Mancini_ShippingZone';
        $this->_controller = 'adminhtml_import';
    }

    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Import\Export');
    }

    /**
     * @return ShippingZone
     */
    protected function getShippingZone()
    {
        return $this->_coreRegistry->registry('shipping_zone');
    }

    /**
     * @return string
     */
    public function getBackUrl()
    {
        $shippingZone = $this->getShippingZone();
        if ($shippingZone) {
            return $this->getUrl('*/index/edit', array('id' => $shippingZone->getId()));
        }

        return $this->getUrl('*/index/index');
    }
}
