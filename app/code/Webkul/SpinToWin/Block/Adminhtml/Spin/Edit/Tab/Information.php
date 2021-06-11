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

class Information extends Generic
{
    protected $_template = 'tab/information.phtml';

    /**
     * Spin Model
     *
     * @var \Webkul\SpinToWin\Model\Info
     */
    public $spinModel;

    /**
     * Website Factory
     *
     * @var \Magento\Store\Model\WebsiteFactory
     */
    public $websiteFactory;

    /**
     * construct
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Framework\Data\FormFactory $formFactory
     * @param \Magento\Store\Model\WebsiteFactory $websiteFactory
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Store\Model\WebsiteFactory $websiteFactory,
        array $data = []
    ) {
        parent::__construct($context, $registry, $formFactory, $data);
        $this->spinModel = $this->_coreRegistry->registry('spininfo');
        $this->websiteFactory = $websiteFactory;
    }

    /**
     * Get Spin Info
     *
     * @return \Webkul\SpinToWin\Model\Info
     */
    public function getSpinInfo()
    {
        return $this->spinModel;
    }

    /**
     * Get Websites
     *
     * @return array
     */
    public function getWebsites()
    {
        return $this->websiteFactory->create()->getCollection()->toOptionArray();
    }
}
