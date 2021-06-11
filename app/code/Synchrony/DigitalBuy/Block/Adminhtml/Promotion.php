<?php

namespace Synchrony\DigitalBuy\Block\Adminhtml;

/**
 * Promotion Grid Container Block
 */
class Promotion extends \Magento\Backend\Block\Widget\Grid\Container
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {

        $this->_controller = 'adminhtml_promotion';
        $this->_blockGroup = 'Synchrony_DigitalBuy';
        $this->_headerText = __('Synchrony Promotions');
        $this->_addButtonLabel = __('Add New Rule');
        parent::_construct();
    }
}
