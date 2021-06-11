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

namespace Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab;

use Magento\Backend\Block\Widget\Form\Generic;

class Trigger extends Generic
{
    protected $_template = 'tab/trigger.phtml';

    /**
     * URL interface
     *
     * @var \Magento\Framework\UrlInterface
     */
    public $_urlInterface;

    /**
     * Spin Model
     *
     * @var \Webkul\SpinToWin\Model\Info
     */
    public $spinModel;

    /**
     * Construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\UrlInterface $urlInterface
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\UrlInterface $urlInterface,
        \Magento\Framework\Data\FormFactory $formFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->_urlInterface = $urlInterface;
        $this->spinModel = $this->_coreRegistry->registry('spininfo');
    }

    /**
     * Visibiliy
     *
     * @return \Webkul\SpinToWin\Model\Visibility
     */
    public function getVisibility()
    {
        return $this->spinModel->getVisibility();
    }

    /**
     * Button
     *
     * @return \Webkul\SpinToWin\Model\Button
     */
    public function getButton()
    {
        return $this->spinModel->getButton();
    }

    /**
     * Coupon
     *
     * @return \Webkul\SpinToWin\Model\Coupon
     */
    public function getCoupon()
    {
        return $this->spinModel->getCoupon();
    }

    /**
     * URL
     *
     * @return string
     */
    public function getUploadUrl()
    {
        return $this->_urlInterface->getUrl('catalog/product_gallery/upload');
    }

    /**
     * Get path of uploaded images
     *
     * @return string
     */
    public function getMediaUrl($filePath)
    {
        return $this->_urlBuilder->getBaseUrl([
            '_type' => \Magento\Framework\UrlInterface::URL_TYPE_MEDIA
            ]).$filePath;
    }
}
