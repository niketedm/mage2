/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
/*browser:true*/
/*global define*/
define(
    [
        'uiComponent',
        'Magento_Checkout/js/model/shipping-rates-validator',
        'Magento_Checkout/js/model/shipping-rates-validation-rules',
        '../../model/shipping-rates-validator/zonerate',
        '../../model/shipping-rates-validation-rules/zonerate'
    ],
    function (
        Component,
        defaultShippingRatesValidator,
        defaultShippingRatesValidationRules,
        zonerateShippingRatesValidator,
        zonerateShippingRatesValidationRules
    ) {
        "use strict";
        defaultShippingRatesValidator.registerValidator('zonerate', zonerateShippingRatesValidator);
        defaultShippingRatesValidationRules.registerRules('zonerate', zonerateShippingRatesValidationRules);
        return Component;
    }
);
