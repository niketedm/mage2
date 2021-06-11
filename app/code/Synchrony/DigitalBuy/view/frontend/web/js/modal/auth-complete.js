define([
    'jquery',
    'Magento_Customer/js/customer-data',
    'Magento_Checkout/js/checkout-data'
], function ($, customerData, checkoutData) {
    'use strict';

    var options,
        redirectFired = false;

    var redirectOnLoad = function (url) {
        var _redirect = function () {
            if (redirectFired) {
                return;
            }
            redirectFired = true;
            window.location.replace(url);
        };
        $(window).on('load', _redirect);
        setTimeout(_redirect, 5000);
    };

    return {
        init: function () {
            var currentAddressData = {}, setterMethod;
            var redirectUrl = options.failureUrl;

            if (options.isSuccessful) {
                redirectUrl = options.successUrl;
                // init customer data again to workaround core bug of wiping checkout data
                customerData.init();
                //pre-select payment method
                checkoutData.setSelectedPaymentMethod(options.paymentMethodCode);

                // update addresses
                if (!$.isEmptyObject(options.addressData)) {
                    if (options.createAddressAsNew) {
                        if (options.isQuoteVirtual) {
                            checkoutData.setNewCustomerBillingAddress(options.addressData);
                            checkoutData.setSelectedBillingAddress('new-customer-address');
                        } else {
                            checkoutData.setNewCustomerShippingAddress(options.addressData);
                            checkoutData.setSelectedShippingAddress('new-customer-address');
                        }
                    }
                    if (options.isQuoteVirtual) {
                        currentAddressData = checkoutData.getBillingAddressFromData();
                        checkoutData.setBillingAddressFromData($.extend(currentAddressData, options.addressData));
                    } else {
                        currentAddressData = checkoutData.getShippingAddressFromData();
                        checkoutData.setShippingAddressFromData($.extend(currentAddressData, options.addressData));
                    }
                }
            }

            redirectOnLoad(redirectUrl);
        },

        'Synchrony_DigitalBuy/js/modal/auth-complete': function (settings) {
            options = settings;
            this.init();
        }
    }
});
