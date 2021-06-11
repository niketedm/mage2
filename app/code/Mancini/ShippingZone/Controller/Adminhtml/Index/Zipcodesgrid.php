<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Controller\Result\Raw;
use Magento\Framework\Controller\Result\RawFactory;
use Magento\Framework\View\LayoutFactory;
use Mancini\ShippingZone\Model\ShippingZone;

class Zipcodesgrid extends Action
{
    /** @var RawFactory */
    protected $resultRawFactory;

    /** @var LayoutFactory */
    protected $layoutFactory;

    /**
     * @param Context $context
     * @param RawFactory $resultRawFactory
     * @param LayoutFactory $layoutFactory
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        LayoutFactory $layoutFactory
    ) {
        parent::__construct($context);
        $this->resultRawFactory = $resultRawFactory;
        $this->layoutFactory = $layoutFactory;
    }

    public function execute()
    {
        $shippingZone = $this->_initShippingZone();
        if (!$shippingZone) {
            /** @var Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            return $resultRedirect->setPath('shippingzone/index/index', ['_current' => true, 'id' => null]);
        }
        /** @var Raw $resultRaw */
        $resultRaw = $this->resultRawFactory->create();
        return $resultRaw->setContents(
            $this->layoutFactory->create()->createBlock(
                'Mancini\ShippingZone\Block\Adminhtml\ShippingZone\Tab\Zipcodes',
                'shipping.zone.zipcodes.grid'
            )->toHtml()
        );
    }

    protected function _initShippingZone()
    {
        $id = $this->getRequest()->getParam('id');
        /** @var ShippingZone $model */
        $model = $this->_objectManager->create('Mancini\ShippingZone\Model\ShippingZone');

        // 2. Initial checking
        if ($id) {
            $model->load($id);
            if (!$model->getId()) {
                $this->messageManager->addError(__('This block no longer exists.'));
                /** @var Redirect $resultRedirect */
                $resultRedirect = $this->resultRedirectFactory->create();
                return $resultRedirect->setPath('shippingzone/index/index');
            }
        }

        $this->_objectManager->get('Magento\Framework\Registry')->register('shipping_zone', $model);

        return $model;
    }
}
