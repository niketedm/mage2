<?php
namespace Mancini\ShippingZone\Block\Adminhtml\Zone;

class Edit extends \Magento\Backend\Block\Widget\Form\Container
{
    protected $_coreRegistry;

    public function __construct(
        \Magento\Backend\Block\Widget\Context $context,
        \Magento\Framework\Registry $coreRegistry,
        array $data = []
    )
    {
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
        $this->buttonList->add(
            'download-zones',
            [
                'label' => __('Download Shipping Zones'),
                'class' => 'download',
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('shippingzone/zone/downloadzone')),
                'class' => 'action-secondary',
            ],
            -100
        );


        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Mancini_ShippingZone';
        $this->_controller = 'adminhtml_zone';
    }

    /**
     * Get header text
     *
     * @return \Magento\Framework\Phrase
     */
    public function getHeaderText()
    {
        return __('Import\Export');
    }


    public function getBackUrl(){
        return $this->getUrl('*/index/index');
    }
}
