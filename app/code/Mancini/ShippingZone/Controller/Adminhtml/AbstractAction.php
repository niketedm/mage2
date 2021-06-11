<?php

namespace Mancini\ShippingZone\Controller\Adminhtml;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\ForwardFactory;
use Magento\Directory\Model\Region;
use Magento\Framework\App\Response\Http\FileFactory;
use Magento\Framework\Controller\Result\Redirect;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Registry;
use Magento\Framework\View\Result\LayoutFactory;
use Magento\Framework\View\Result\PageFactory;
use Mancini\ShippingZone\Helper\Data;
use Mancini\ShippingZone\Model\ShippingZone\ZipcodesFactory;
use Mancini\ShippingZone\Model\ShippingZoneFactory;

abstract class AbstractAction extends Action
{
    /** @var PageFactory */
    protected   $resultPageFactory;

    /** @var LayoutFactory */
    protected   $resultLayoutFactory;

    /** @var ForwardFactory */
    protected   $resultForwardFactory;

    /** @var RedirectFactory */
    protected   $resultRedirectFactory;

    /** @var ShippingZoneFactory */
    protected   $shippingZoneFactory;

    /** @var ZipcodesFactory */
    protected   $zipcodesFactory;

    /** @var Registry */
    protected $coreRegistry;

    /** @var Data */
    protected   $helper;

    /** @var Region */
    protected   $region;

    /** @var FileFactory */
    protected $fileFactory;

    /**
     * AbstractAction constructor.
     * @param Context $context
     * @param Registry $coreRegistry
     * @param PageFactory $resultPageFactory
     * @param LayoutFactory $resultLayoutFactory
     * @param ForwardFactory $resultForwardFactory
     * @param ShippingZoneFactory $shippingZoneFactory
     * @param ZipcodesFactory $zipcodesFactory
     * @param Region $region
     * @param Data $helper
     * @param FileFactory $fileFactory
     */
    public  function __construct(
        Context $context,
        Registry $coreRegistry,
        PageFactory  $resultPageFactory,
        LayoutFactory $resultLayoutFactory,
        ForwardFactory  $resultForwardFactory,
        ShippingZoneFactory $shippingZoneFactory,
        ZipcodesFactory $zipcodesFactory,
        Region $region,
        Data $helper,
        FileFactory $fileFactory
    ) {
        $this->resultPageFactory     = $resultPageFactory;
        $this->resultLayoutFactory   = $resultLayoutFactory;
        $this->resultForwardFactory  = $resultForwardFactory;
        $this->shippingZoneFactory   = $shippingZoneFactory;
        $this->zipcodesFactory       = $zipcodesFactory;
        $this->helper                = $helper;
        $this->coreRegistry          = $coreRegistry;
        $this->region                = $region;
        $this->resultRedirectFactory = $context->getResultRedirectFactory();
        $this->fileFactory           = $fileFactory;
        parent::__construct($context);
    }

    /**
     * @return bool
     */
    protected  function _isAllowed()
    {
        return  $this->_authorization->isAllowed('Mancini_ShippingZone::manage_shipping_zone');
    }

    /**
     * @param Redirect $resultRedirect
     * @param null|int $paramCrudId
     * @return Redirect
     */
    protected function _getBackResultRedirect(Redirect $resultRedirect, $paramCrudId = null)
    {
        switch ($this->getRequest()->getParam('back')) {
            case 'edit':
                $resultRedirect->setPath(
                    '*/*/edit',
                    [
                        'id' => $paramCrudId,
                        '_current' => true,
                    ]
                );
                break;
            case 'new':
                $resultRedirect->setPath('*/*/new', ['_current' => true]);
                break;
            default:
                $resultRedirect->setPath('*/*/');
        }

        return $resultRedirect;
    }
}
