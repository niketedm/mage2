<?php
/**
 * Mancini
 * For changing the template of the Cart
 */ 
namespace Mancini\Cart\Block;
 
class AbstractCart
{
 
	public function afterGetItemRenderer(\Magento\Checkout\Block\Cart\AbstractCart $subject, $result)
	{
        $result->setTemplate('Mancini_Cart::cart/item/default.phtml');
    	return $result;
	}
}