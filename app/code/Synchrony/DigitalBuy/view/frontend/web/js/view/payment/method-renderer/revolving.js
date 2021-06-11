define([
    'jquery',
    'Magento_Checkout/js/view/payment/default',
    'Synchrony_DigitalBuy/js/action/redirect-with-loader',
    'mage/url',
    'Synchrony_DigitalBuy/js/model/storage',
    'uiLayout'
], function ($, Component, redirectOnSuccessAction, url, storage, layout) {
    'use strict';

    return Component.extend({
        defaults: {
            redirectAfterPlaceOrder: false,
            template: 'Synchrony_DigitalBuy/payment/revolving'
        },

        /**
         * Initialize child elements
         *
         * @returns {Component} Chainable.
         */
        initChildren: function () {
            this._super();
            this.createMarketingBlockComponent();

            return this;
        },

        /**
         * Create child message renderer component
         *
         * @returns {Component} Chainable.
         */
        createMarketingBlockComponent: function () {
            if(this.getIsPromotionBlockEnabled()) {
                if (this.getIsPromotionContentStatic()) {
                    return this;
                } else if (this.getIsPromotionContentDynamic()) {
                    var messagesComponent = {
                        parent: this.name,
                        name: 'synchrony-marketing-block-checkout',
                        displayArea: 'marketing-block',
                        component: 'Synchrony_DigitalBuy/js/view/payment/marketing',
                        config: {
                            paymentMethodTitle: this.getTitle(),
                            blockConfig : this.getPriceConfig(),
                            template: this.getPriceConfig().template
                        }
                    };
                    layout([messagesComponent]);
                }
            }
            return this;
        },

        /**
         * Get Price Config settings
         *
         */
        getPriceConfig: function() {
            return window.checkoutConfig.payment[this.getCode()].marketingBlockConfig;
        },

        /**
         * check if promotion block is enabled
         *
         * @returns {boolean}
         */
        getIsPromotionBlockEnabled: function() {
             if(window.checkoutConfig.payment[this.getCode()].marketingBlockConfig){
                 return true;
             }
             return false;
        },

        /**
         * Check if promotion content is static
         *
         * @returns {boolean}
         */
        getIsPromotionContentStatic: function() {
            if(window.checkoutConfig.payment[this.getCode()].marketingBlockConfig &&
                window.checkoutConfig.payment[this.getCode()].marketingBlockConfig.static == true){
                return true;
            }
            return false
        },

        /**
         * Check if promotion content is dynamic
         *
         * @returns {boolean}
         */
        getIsPromotionContentDynamic: function() {
            if(window.checkoutConfig.payment[this.getCode()].marketingBlockConfig &&
                window.checkoutConfig.payment[this.getCode()].marketingBlockConfig.dynamic == true){
                return true;
            }
            return false;
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
         * Get the account number / card as mask
         * @returns {*}
         */
        getMaskedCard: function () {
            return window.checkoutConfig.payment[this.getCode()].accountNumber;
        },

        /**
         * Check if Address match note is allowed to show
         *
         * @returns {bool}
         */
        canShowAddressMatchNote: function () {
            return window.checkoutConfig.payment[this.getCode()].canShowAddressMatchNote;
        },

        /**
         * Get Address match note
         *
         * @returns {string}
         */
        getAddressMatchNote: function() {
            return window.checkoutConfig.payment[this.getCode()].addressMatchNote;
        },

        /**
         * Check if cart is eligible for payment
         *
         * @returns {bool}
         */
        isCartValid: function() {
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
