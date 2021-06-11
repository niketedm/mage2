<?php

namespace Synchrony\DigitalBuy\Model\Source\DynamicPricing;

use Magento\Cms\Model\ResourceModel\Block\CollectionFactory;
use Magento\Framework\Option\ArrayInterface;

class CmsBlocks implements ArrayInterface
{
    private $_options;

    /**
     * Block collection factory
     *
     * @var CollectionFactory
     */
    private $_blockCollectionFactory;

    /**
     * Construct
     *
     * @param CollectionFactory $blockCollectionFactory
     */
    public function __construct(CollectionFactory $blockCollectionFactory)
    {
        $this->_blockCollectionFactory = $blockCollectionFactory;
    }

    /**
     * @return array
     */
    public function toOptionArray()
    {
        if (!$this->_options) {
            $this->_options = [
                ['value' => '', 'label' => __('Please select a static block.')]
            ];
            $blocks = $this->_blockCollectionFactory->create();
            foreach ($blocks as $block) {
                $this->_options[] = ['value' => $block->getIdentifier(), 'label' => $block->getTitle()];
            }
        }
        return $this->_options;
    }
}
