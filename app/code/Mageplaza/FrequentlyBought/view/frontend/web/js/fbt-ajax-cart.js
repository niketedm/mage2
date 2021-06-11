/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_FrequentlyBought
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

define([
    'jquery',
    'Magento_Catalog/js/price-utils',
    'Magento_Customer/js/customer-data',
    'mageplaza/core/jquery/popup'
], function ($, priceUtils, customerData) {
    'use strict';

    var mpFbtPopupContent = $('#mpfbt-popup-content');

    $.widget('mageplaza.fbtAjaxCart', {
        options: {
            processStart: null,
            processStop: null,
            bindSubmit: true,
            minicartSelector: '[data-block="minicart"]',
            messagesSelector: '[data-placeholder="messages"]',
            productStatusSelector: '.stock.available',
            addToCartButtonSelector: '.action.mpfbt-tocart',
            addToCartButtonDisabledClass: 'disabled',
            addToCartButtonTextWhileAdding: '',
            addToCartButtonTextAdded: '',
            addToCartButtonTextDefault: ''
        },
        cache: {
            priceObject: {}
        },

        /**
         * @inheritDoc
         */
        _create: function () {
            this._EventListener();
            if (this.options.usePopup === '1') {
                this.addToCart();
            }
        },

        _EventListener: function () {
            var self = this;
            this.element.on(
                'change', '#mpfbt-popup-content .mpfbt-product-input', function () {
                    self._reloadTotalPrice();
                }
            );
            this.element.on(
                'change', '#mpfbt-popup-content .mageplaza-fbt-grouped .mageplaza-fbt-grouped-qty', function () {
                    self._reloadGroupedPrice($(this));
                }
            );
            this.element.find('#mpfbt-popup-content .product-custom-option').on('change', this._onOptionChanged.bind(this));
            $('.mageplaza-fbt-add-to-cart button.action.mpfbt-tocart').on('click', function (e) {
                if (self.options.usePopup === '1') {
                    self._showPopup(e);
                }
            });
        },

        _showPopup: function (event) {
            var popupTrigger = $('#mpfbt-open-popup'),
                count = 0;

            event.preventDefault();
            $('.mageplaza-fbt-rows ul li').each(function () {
                var $widget = $(this),
                    productId;

                productId = $widget.find('.related-checkbox').data('mageplaza-fbt-product-id');
                if (!$widget.find('.related-checkbox').is(':checked')) {
                    $('#mpfbt-product-input-' + productId)[0].value = '';
                    mpFbtPopupContent.find('.mpfbt-popup-product-detail[data-mpfbt-popup-product-id="'+productId+'"]').hide();
                } else {
                    count++;
                    $('#mpfbt-product-input-' + productId)[0].value = 1;
                    mpFbtPopupContent.find('.mpfbt-popup-product-detail[data-mpfbt-popup-product-id="'+productId+'"]').show();
                }
            });
            popupTrigger.magnificPopup({
                type: 'inline',
                midClick: true,
                closeBtnInside: true,
                callbacks: {
                    open: function () {
                        $('.mpfbt-total-items-value').text(count);
                        $('button.mpfbt-btn-continue').on('click', function (e) {
                            e.preventDefault();
                            $.magnificPopup.close();
                        })
                    }
                }
            });
            popupTrigger.click();
        },

        addToCart: function () {
            $('button#mpfbt-btn-addtocart').on('click', function (e) {
                var form = $('#mageplaza-fbt-form-popup'),
                    actionUrl = form.attr('action'),
                    params = form.serialize(),
                    validate = form.validation('isValid');

                e.preventDefault();

                if (!validate) {
                    return;
                }
                $.ajax({
                    url: actionUrl,
                    data: params,
                    type: 'post',
                    dataType: 'json',
                    showLoader: true,
                    success: function (res) {
                        $('.mpfbt-message').remove();
                        if (res.error) {
                            $.each(res.message, function (key, value) {
                                $('.page.messages').prepend('<div class="mpfbt-message message-error error message">' + value + '</div>');
                            });
                        }

                        if (res.success) {
                            $('.page.messages').prepend('<div class="mpfbt-message message-success success message">' + res.message + '</div>');

                            var sections = ['cart'];
                            customerData.invalidate(sections);
                            customerData.reload(sections, true);
                        }

                        $.magnificPopup.close();
                    }
                });
            });
        },

        _onOptionChanged: function (event) {
            var optionPrice = 0,
                changes = {},
                element = $(event.target),
                optionName = element.prop('name'),
                optionType = element.prop('type'),
                parentElement = element.closest('tr'),
                inputElement = parentElement.find('.mpfbt-product-input'),
                productId = inputElement.attr('data-mpfbt-popup-product-id'),
                productPrice = parseFloat(inputElement.attr('data-price-amount'));
            switch (optionType) {
                case 'text':

                case 'textarea':
                    optionPrice = parseFloat(element.closest('div.field').find('.price-wrapper').attr('data-price-amount'));
                    if (element.val()) {
                        changes[optionName] = optionPrice;
                    } else {
                        changes[optionName] = 0;
                    }
                    break;

                case 'radio':
                    optionPrice = parseFloat(element.attr('price'));
                    if (element.is(':checked')) {
                        changes[optionName] = optionPrice;
                    }
                    break;
                case 'select-one':
                    if (element.find(":selected").attr('price')) {
                        optionPrice = parseFloat(element.find(":selected").attr('price'));
                    }
                    changes[optionName] = optionPrice;
                    break;

                case 'select-multiple':
                    _.each(
                        element.find('option'), function (option) {
                            if ($(option).is(':selected')) {
                                optionPrice += parseFloat($(option).attr('price'));
                            }
                        }
                    );
                    changes[optionName] = optionPrice;
                    break;

                case 'checkbox':
                    _.each(
                        element.closest('.options-list').find('.product-custom-option'), function (option) {
                            if ($(option).is(':checked')) {
                                optionPrice += parseFloat($(option).attr('price'));
                            }
                        }
                    );
                    changes[optionName] = optionPrice;
                    break;

                case 'file':
                    // Checking for 'disable' property equal to checking DOMNode with id*="change-"
                    if (element.val() && !element.prop('disabled')) {
                        optionPrice = parseFloat(element.closest('div.field').find('.price-wrapper').attr('data-price-amount'));
                    }
                    changes[optionName] = optionPrice;
                    break;
            }
            $.extend(this.cache.priceObject, changes);
            _.each(
                this.cache.priceObject, function (value, key) {
                    var parentElementUpdate = $('[name="' + key + '"]').closest('tr'),
                        productIdUpdate = parentElementUpdate.find('.mpfbt-product-input').attr('data-mpfbt-popup-product-id');
                    if (productId === productIdUpdate) {
                        productPrice += parseFloat(value);
                    }
                }
            );
            parentElement.find('.item-price').attr('data-price-amount', productPrice);
            this._reloadTotalPrice();
        },

        _reloadGroupedPrice: function ($this) {
            var price = 0,
                _this = this,
                totalPrice = 0,
                productId = $this.closest('.mpfbt-popup-product-detail').find('.mpfbt-product-input').attr('data-mpfbt-popup-product-id');
            if ($this.val() > 0) {
                price = parseFloat($this.val()) * parseFloat($this.attr('data-child-product-price-amount'));
            }
            $this.attr('data-child-product-price-total', price);
            $('#mpfbt-popup-content #mageplaza-fbt-super-product-table-' + productId + ' .mageplaza-fbt-grouped-qty').each(
                function () {
                    totalPrice += parseFloat($(this).attr('data-child-product-price-total'));
                }
            );
            $('.mageplaza-fbt-price-' + productId).attr('data-price-amount', totalPrice);

            _this._reloadTotalPrice();
        },

        _reloadTotalPrice: function () {
            var totalPrice = 0,
                _this = this;

            $('#mpfbt-popup-content .mpfbt-product-input').each(
                function () {
                    if ($(this).val()) {
                        totalPrice += parseFloat($(this).closest('tr').find('.item-price').attr('data-price-amount'));
                    }
                    var priceElement = $(this).closest('tr').find('.item-price'),
                        priceItem = $(priceElement).attr('data-price-amount');
                    $(priceElement).empty().append(_this._getFormattedPrice(priceItem));
                }
            );

            $('.mageplaza-fbt-rows .related-checkbox').each(
                function () {
                    var priceElement = $(this).closest('li').find('.item-price'),
                        priceItem = $(priceElement).attr('data-price-amount');
                    $(priceElement).empty().append(_this._getFormattedPrice(priceItem));
                }
            );

            $('.mageplaza-fbt-price-wrapper').attr('data-price-amount', totalPrice);
            $('.mageplaza-fbt-price').empty().append(_this._getFormattedPrice(totalPrice));
        },

        _getFormattedPrice: function (price) {
            return priceUtils.formatPrice(price, this.options.priceFormat);
        },
    });

    return $.mageplaza.fbtAjaxCart;
});