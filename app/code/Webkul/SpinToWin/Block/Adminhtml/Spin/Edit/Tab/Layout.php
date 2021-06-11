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

class Layout extends Generic
{
    protected $_template = 'tab/layout.phtml';

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
     * Get Layout
     *
     * @return \Webkul\SpinToWin\Model\Layout
     */
    public function getLayoutData()
    {
        return $this->spinModel->getLayout();
    }
}
