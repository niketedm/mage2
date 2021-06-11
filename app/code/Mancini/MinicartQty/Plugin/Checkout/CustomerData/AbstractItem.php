<?php
	/**
	 * Add brand name in the Minicart
	 *
	 * @category    Mancini
	 * @package     Mancini_MinicartQty
	 * @author      Mancini
	 *
	 */
declare(strict_types=1);

namespace Mancini\MinicartQty\Plugin\Checkout\CustomerData;

class AbstractItem
{
    /**
     * @var Repository
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

    /**
     * Constructor
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper 
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\Pricing\Helper\Data $priceHelper
    ) {
        $this->_priceHelper = $priceHelper;
        $this->_productRepository = $productRepository;
    }

    /**
     * Add Brand to frontend storage (CustomerData)
     *
     * @param $subject
     * @param $item
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function afterGetItemData(
        \Magento\Checkout\CustomerData\AbstractItem $subject, 
        $result, 
        \Magento\Quote\Model\Quote\Item $item
    ) {
        if($item->getProduct()->getSpecialPrice()){
            $priceStrike = $item->getProduct()->getPrice();
            $data['strikeprice']    =   $this->_priceHelper->currency($priceStrike, true, false);
        }

        $product = $this->_productRepository->getById($item->getProduct()->getEntityId());
        if ($item->getProduct()->getTypeId() == "simple") {
            if($product->getSize()){
                $data['prdsize'] = $product->getAttributeText('size');
            }
        }
        if(null !== $item->getProduct()->getAttributeText('brand')){
            $data['brand'] = $item->getProduct()->getAttributeText('brand');
        }

        return \array_merge(
            $result,
            $data
        );
    }
}
