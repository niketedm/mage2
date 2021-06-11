define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_Checkout/js/model/quote',
    'Magento_Catalog/js/price-utils',
    'mage/dropdown',
    'mage/translate',
],function (ko, Component, $, quote, priceUtils) {
    'use strict';

    return Component.extend({
        dialog: false,

        defaults: {
            showSection: true,
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe('showSection');
            return this;
        },

        /**
         * Calculate Monthly amount
         *
         * @return (float)
         */
        getMonthlyAmount:function () {
            let monthlyAmount = Math.ceil(this.getCalculatedTotalAmount() / parseInt(this.blockConfig.term));
            return this.getFormattedPrice(monthlyAmount);

        },

        /**
         * Calculate Total amount
         *
         * @return (float)
         */
        getCalculatedTotalAmount:function () {
            var grandTotal = parseFloat(quote.totals().base_grand_total);
            this.canShowSection(grandTotal);
            if (this.blockConfig.promotionCalculationType != 3) {
                return grandTotal
            } else {
                return  grandTotal + (grandTotal * parseFloat(this.blockConfig.apr) /  100);
            }
        },

        /**
         * Get Total amount
         *
         * @return (float)
         */
        getTotalAmount:function () {
            return this.getFormattedPrice(this.getCalculatedTotalAmount());
        },

        /**
         * @param {*} price
         * @return {*|String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, quote.getPriceFormat());
        },

        /**
         * Check Flag to show/hide marketing block section
         */
        canShowSection:function () {
            if (parseFloat(quote.totals().base_grand_total).toFixed(2)
                < parseFloat(this.blockConfig.minAmount).toFixed(2)) {
                this.showSection(false);
            } else {
                this.showSection(true);
            }
            return this.showSection();
        },

        /**
         * Get Minimum amount config value
         * @return {String}
         */
        getMinAmount: function () {
            return priceUtils.formatPrice(this.blockConfig.minAmount);
        },

        /**
         * Get Cms page link
         * @return {boolean|String}
         */
        getCmsPageLink: function () {
            if (this.blockConfig.links.cmsPageLink) {
                return this.blockConfig.links.cmsPageLink;
            }
            return false;
        },

        /**
         * Get Apply page link
         * @return {boolean|String}
         */
        getApplyNowLink: function () {
            if (this.blockConfig.links.applyNowLink) {
                return this.blockConfig.links.applyNowLink;
            }
            return false;
        },


        /**
         * Get price is excluded from text
         *
         * @return {boolean}
         */
        getPriceIsExcludedFrom: function () {
            return false;
        },

        setDialog: function(element) {
            this.dialog = $(element);
        },

        /**
         * Close promotion content dialog.
         */
        closePromotionContentDialog: function () {
            if (this.dialog) {
                this.dialog.dropdownDialog('close');
            }
        }
    });
});
