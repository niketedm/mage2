/* @api */
define([
    'uiComponent',
    'Magento_Checkout/js/model/payment/renderer-list'
], function (Component, rendererList) {
    'use strict';

    rendererList.push({
        type: 'synchrony_digitalbuy_installment',
        component: 'Synchrony_DigitalBuy/js/view/payment/method-renderer/installment'
    });

    /** Add view logic here if needed */
    return Component.extend({});
});
