<?php

namespace Mancini\ProductConsole\Block\Adminhtml\Import;

use Magento\Backend\Block\Widget\Form\Container;
use Magento\Framework\Phrase;

class Edit extends Container
{
    /**
     * Get header text
     *
     * @return Phrase
     */
    public function getHeaderText()
    {
        return __('Import');
    }

    /**
     * Internal constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();

        $this->buttonList->remove('back');
        $this->buttonList->remove('reset');
        $this->buttonList->update('save', 'label', __('Import'));

        $this->buttonList->add(
            'reindex-products',
            [
                'label' => __('Reindex Products'),
                'on_click' => sprintf("location.href = '%s';", $this->getUrl('product_console/reindex/products')),
                'class' => 'action-secondary save',
            ],
            -100
        );

        $this->_objectId = 'import_id';
        $this->_blockGroup = 'Mancini_ProductConsole';
        $this->_controller = 'adminhtml_import';
    }
}
