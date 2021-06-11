<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Block\Adminhtml\Spin\Edit;

class Form extends \Magento\Backend\Block\Widget\Form\Generic
{
    /**
     * Init form
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spin_form');
        $this->setTitle(__('Spin Campaign Information'));
    }

    /**
     * Prepare form
     *
     * @return $this
     */
    protected function _prepareForm()
    {
        $model = $this->_coreRegistry->registry('spininfo');
        $form = $this->_formFactory->create(
            ['data' => [
                        'id' => 'edit_form',
                        'enctype' => 'multipart/form-data',
                        'action' => $this->getData('action'),
                        'method' => 'post']
                    ]
        );
        $form->setUseContainer(true);
        $this->setForm($form);
        return parent::_prepareForm();
    }
}
