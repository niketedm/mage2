<?php

namespace Mancini\ShippingZone\Model\Config\Source;

use Magento\Catalog\Model\Product\AttributeSet\Options;
use Magento\Framework\Option\ArrayInterface;

class AttributeSet implements ArrayInterface
{
    /** @var array */
    protected $_options;

    /** @var Options */
    protected $attributeSetOptions;

    /**
     * @param Options $attributeSetOptions
     */
    public function __construct(Options $attributeSetOptions)
    {
        $this->attributeSetOptions = $attributeSetOptions;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [];
            foreach ($this->attributeSetOptions->toOptionArray() as $attributeSet) {
                $id = $attributeSet['value'];
                $name = $attributeSet['label'];
                if ($id != 0) {
                    $this->_options[] = ['value' => $id, 'label' => $name];
                }
            }
        }
        return $this->_options;
    }
}
