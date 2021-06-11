<?php
/**
 * Anowave Magento 2 FAQ
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Faq\Block;

use Magento\Framework\Registry;
use Magento\Backend\Block\Template\Context;

/**
 * Adminhtml customer groups edit form
 */
class Panel extends \Magento\Backend\Block\Template
{
    /**
     * @var string
     */
    protected $_template = 'tab.phtml';
 
    /**
     * Core registry
     *
     * @var Registry
     */
    protected $_coreRegistry = null;
 
    /**
     * Cosntructor 
     * 
     * @param Context $context
     * @param Registry $registry
     * @param array $data
     */
    public function __construct
    (
        Context $context,
        Registry $registry,
        array $data = []
    )
    {
        $this->_coreRegistry = $registry;
        
        parent::__construct($context, $data);
    }
 
    /**
     * Retrieve product
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getProduct()
    {
        return $this->_coreRegistry->registry('current_product');
    }
    
    public function toHtml()
    {
    	return $this->getLayout()->createBlock('Anowave\Faq\Block\Form')->setData(array('product_id' => $this->getProduct()->getId()))->toHtml();
    }
}