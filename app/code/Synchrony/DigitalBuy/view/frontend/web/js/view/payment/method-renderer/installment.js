define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Synchrony_DigitalBuy/js/action/redirect-with-loader',
    'mage/url',
    'Synchrony_DigitalBuy/js/model/storage'
], function ($, Component, redirectOnSuccessAction, url, storage) {
    'use strict';

    return Component.extend({
        defaults: {
            redirectAfterPlaceOrder: false,
            template: 'Synchrony_DigitalBuy/payment/installment'
        },

        /**
         * Get Order Completion page URL
         *
         * @returns {string}
         */
        getOrderCompletionUrl: function() {
            return window.checkoutConfig.payment[this.getCode()].redirectUrl;
        },

        /**
         * After place order callback.
         * Redirecting user to DigitalBuy payment page
         * skipCustomerDataReload flag is used to suppress customer data reload (checking in corresponding mixin),
         * which is redundant in first place as well as leads to session write race condition for our case
         * (in case if session locking is disabled)
         * Actual redirect is executed through timeout to still give some time for customer data to invalidate
         * as it's not properly chained to order place from core logic standpoint
         */
        afterPlaceOrder: function () {
            window.skipCustomerDataReload = true;
            var completionUrl = this.getOrderCompletionUrl();
            setTimeout(function () {
                redirectOnSuccessAction.execute(completionUrl);
                window.skipCustomerDataReload = false;
             }, 500);
        },

        /**
         * Check if cart is eligible for payment
         *
         * @returns {bool}
         */
        isCartValid: function () {
            return window.checkoutConfig.payment[this.getCode()].isCartValid;
        },

        /**
         * Get cart validation error message
         *
         * @returns {string}
         */
        getCartValidationMsg: function () {
            return window.checkoutConfig.payment[this.getCode()].cartValidationMsg;
        },

        /**
         * Redirect user to cart
         */
        redirectToCart: function () {
            storage.set('intent-to-use', true);
            window.location.href = url.build('checkout/cart/');
        }
    });
});
