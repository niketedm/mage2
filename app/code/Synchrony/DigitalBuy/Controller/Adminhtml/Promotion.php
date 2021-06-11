<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml;

/**
 * Promotions controller
 */
abstract class Promotion extends \Magento\Backend\App\Action
{
    /**
     * {@inheritdoc}
     */
    const ADMIN_RESOURCE = 'Synchrony_DigitalBuy::promotion';

    /**
     * Initiate action
     *
     * @return this
     */
    protected function _initAction()
    {
        $this->_view->loadLayout();
        $this->_setActiveMenu('Synchrony_DigitalBuy::promotion')
            ->_addBreadcrumb(__('Synchrony Promotion Rules'), __('Synchrony Promotion Rules'));
        return $this;
    }
}
