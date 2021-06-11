define([
    'ko',
    'uiComponent',
    'jquery',
    'Magento_Catalog/js/price-utils',
    'mage/dropdown',
    'mage/translate',
],function (ko, Component, $, priceUtils) {
    'use strict';

    return Component.extend({
        dialog: false,

        defaults: {
            priceBoxes :false,
            showSection: true,
            priceFormat: {}
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            var dataPriceBoxSelector = '[data-role=priceBox]',
                dataProductIdSelector = '[data-product-id='+this.productId+']',
                priceBoxes = $(dataPriceBoxSelector + dataProductIdSelector);
            this.priceBoxes = priceBoxes.filter(function (index, elem) {
                return !$(elem).find('.price-from').length;
            });
            if (this.priceBoxes!==undefined) {
                this.priceFormat = (this.priceBoxes.priceBox('option').priceConfig && this.priceBoxes.priceBox('option').priceConfig.priceFormat) || {};
                this.priceBoxes.on('reloadPrice', this.reloadPrice.bind(this));
            }
        },

        /**
         * @inheritdoc
         */
        initObservable: function () {
            this._super()
                .observe('showSection')
                .observe('productPrice');
            return this;
        },

        /**
         * @inheritdoc
         */
        reloadPrice: function () {
            var priceData = this.priceBoxes.data('magePriceBox').cache.displayPrices;
            this.productPrice(priceData.finalPrice.final);
        },



        /**
         * Calculate Monthly amount
         *
         * @return (float)
         */
        getMonthlyAmount:function () {
            var monthtlyAmount = Math.ceil(this.getCalculatedTotalAmount() / parseInt(this.blockConfig.term));
            return this.getFormattedPrice(monthtlyAmount);

        },

        /**
         * Calculate Total amount
         *
         * @return (float)
         */
        getCalculatedTotalAmount:function () {
            var productPrice = parseFloat(this.productPrice());
            this.canShowSection(productPrice);
            if (this.blockConfig.promotionCalculationType != 3) {
                return productPrice
            } else {
                return productPrice + (productPrice * parseFloat(this.blockConfig.apr) /  100);
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
         * Get Formatted price string
         *
         * @param price
         * @return {String}
         */
        getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, this.priceFormat);
        },

        /**
         * Check if promotion block can be shown
         */
        canShowSection:function () {
            if (parseFloat(this.productPrice).toFixed(2)
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
         * @return {String}
         */
        getPriceIsExcludedFrom: function () {
            return $.mage.__('(excluding taxes and delivery)');
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
