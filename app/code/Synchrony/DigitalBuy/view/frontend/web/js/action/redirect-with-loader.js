define([
    'mage/url',
    'Magento_Checkout/js/model/full-screen-loader'
], function (url, fullScreenLoader) {
    'use strict';

    return {
        /**
         * Redirect with full screen loader
         */
        execute: function (redirectUrl) {
            fullScreenLoader.startLoader();
            window.location.replace(url.build(redirectUrl));
        }
    };
});
