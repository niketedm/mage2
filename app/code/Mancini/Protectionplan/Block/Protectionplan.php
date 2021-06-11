<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\Protectionplan\Block;

class Protectionplan extends \Magento\Framework\View\Element\Template
{
     /**
         * @var \Magento\Framework\Registry
        */
        protected $_registry;

        /**
        * @var attributeSet
        */
        protected $attributeSet;
    /**
     * Constructor
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     * @param \Magento\Framework\View\Element\Template\Context  $context
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        \Magento\Framework\View\Element\Template\Context $context,
        array $data = []
    ) {
        $this->_registry = $registry;
        $this->attributeSet = $attributeSet;
        parent::__construct($context, $data);
    }

     /**
         * Return catalog product object
         * @return \Magento\Catalog\Model\Product|Null
         */
        public function getCurrentProduct()
        {        
            return $this->_registry->registry('current_product');
        } 

        /**
         * Function for getting attribute set name of the current product
         * @return string
         */
        public function getAttributeSetName(){
                $attributeSetRepository = $this->attributeSet->get($this->getCurrentProduct()->getAttributeSetId());
                $attributeSetName   =$attributeSetRepository->getAttributeSetName();
            return  $attributeSetName;
        }
}
