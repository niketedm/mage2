define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    'Magento_Ui/js/modal/alert',
    'Magento_Ui/js/modal/confirm',
    'underscore',
    'jquery/ui',
    'mage/decorate',
    'mage/collapsible',
    'mage/cookies',
    'loader'
], function ($, authenticationPopup, customerData, alert, confirm, _) {
    'use strict';

    return function (widget) {

        $.widget('mage.sidebar', widget, {

            _updateItemQty: function (elem) {
                var itemId = elem.data('cart-item');
                var btnplus = elem.data('btn-plus');
                var btnminus = elem.data('btn-minus');
                this._ajax(this.options.url.update, {
                    'item_id': itemId,
                    'item_qty': $('#cart-item-' + itemId + '-qty').val(),
                    'item_btn_plus': btnplus,
                    'item_btn_minus': btnminus,
                }, elem, this._updateItemQtyAfter);
                // this._customerData();
            },
       /**
         * @param {HTMLElement} elem
         * @private
         */
        _hideItemButton: function (elem) {
            var itemId = elem.data('cart-item');

            $('#update-cart-item-' + itemId).hide('fade', 300);
        },

                /**
         * @param {HTMLElement} elem
         * @private
         */
                 _removeItem: function (elem) {
                    var itemId = elem.data('cart-item');
        
                    this._ajax(this.options.url.remove, {
                        'item_id': itemId
                    }, elem, this._removeItemAfter);
                },
        
        /**
         * @param {HTMLElement} elem
         * @private
         */
         _showItemButton: function (elem) {
            var itemId = elem.data('cart-item'),
                itemQty = elem.data('item-qty');

            if (this._isValidQty(itemQty, elem.val())) {
                $('#update-cart-item-' + itemId).show('fade', 300);
            } else if (elem.val() == 0) { //eslint-disable-line eqeqeq
                this._hideItemButton(elem);
            } else {
                this._hideItemButton(elem);
            }
        },

            /**
             * Update content after update qty
             *
             * @param {HTMLElement} elem
             */
            _updateItemQtyAfter: function (elem) {
                var productData = this._getProductById(Number(elem.data('cart-item')));

                if (!_.isUndefined(productData)) {
                    $(document).trigger('ajax:updateCartItemQty');

                    if (window.location.href === this.shoppingCartUrl) {
                        window.location.reload(false);
                    }
                }
                this._customerData(elem); 
            },

            _customerData : function (elem)  {
                var sections = ['cart'];
                customerData.invalidate(sections);
                customerData.reload(sections, true);
                this._hideItemButton(elem);
            }

        });
        return $.mage.sidebar;
    }
    
});