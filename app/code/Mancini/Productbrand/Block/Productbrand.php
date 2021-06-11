<?php

namespace Mancini\Productbrand\Block;

use Magento\Framework\View\Element\Template;

class Productbrand extends Template
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
     * @param \Magento\Framework\Registry $registry
     * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
     */
    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->attributeSet = $attributeSet;
        $this->_registry = $registry;
    }

    /**
     * Return catalog product object
     *
     * @return \Magento\Catalog\Model\Product
     */
    public function getCurrentProduct()
    {
        return $this->_registry->registry('current_product');
    }

    public function getAttributeSetName()
    {
        $attributeSetRepository = $this->attributeSet->get($this->getCurrentProduct()->getAttributeSetId());
        $attributeSetName       = $attributeSetRepository->getAttributeSetName();
        return  $attributeSetName;
    }
}
