/**
 * Anowave Magento 2 Extra Fee
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
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

define(['jquery','mage/utils/wrapper', 'Magento_Checkout/js/model/quote','Magento_Checkout/js/model/full-screen-loader','Magento_Checkout/js/action/get-totals'], function($, wrapper, quote, fullScreenLoader, getTotalsAction)
{
    'use strict';

    return function(paymentMethod) 
    {
        return wrapper.wrap(paymentMethod, function (originalAction, method) 
        {
        	fullScreenLoader.startLoader();

        	if ('undefined' !== typeof checkout)
        	{
	            jQuery.ajax(checkout.baseUrl + 'fee/checkout/applyPaymentMethod', 
			 	{
	                data: 
	                {
	                	payment_method: method.method
	                },
	                complete: function () 
	                {
	                	getTotalsAction([]);
	                	fullScreenLoader.stopLoader();
	                }
			 	});
        	}

            return originalAction(method);
        });
    };
});