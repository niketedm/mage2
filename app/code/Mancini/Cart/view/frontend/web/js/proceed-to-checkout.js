/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

 define([
    'jquery',
    'Magento_Customer/js/model/authentication-popup',
    'Magento_Customer/js/customer-data',
    "mage/url",
    "mage/cookies",
    'jquery/jquery.cookie'
], function ($, authenticationPopup, customerData, url) {
    'use strict';

    return function (config, element) {
        $(element).click(function (event) {
            var cart = customerData.get('cart'),
                customer = customerData.get('customer');

            event.preventDefault();

            if (!customer().firstname && cart().isGuestCheckoutAllowed === false) {
                url.setBaseUrl(BASE_URL);
            
                $.cookieStorage.set('checkoutlogin', '1');
                location.href = url.build('customer/account/login');
            
                //authenticationPopup.showModal();
               

                return false;
            }
            $(element).attr('disabled', true);
            location.href = config.checkoutUrl;
        });

    };
});
