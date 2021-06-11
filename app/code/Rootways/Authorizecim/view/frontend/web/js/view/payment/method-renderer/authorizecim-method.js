define([
    'Magento_Payment/js/view/payment/cc-form',
    'jquery',
    'Magento_Vault/js/view/payment/vault-enabler',
    'Magento_Checkout/js/action/place-order',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Payment/js/model/credit-card-validation/validator'
], function (
       Component,
        $,
        VaultEnabler
       ) {
    'use strict';
        
    var configAuthorizenet = window.checkoutConfig.payment.rootways_authorizecim_option;
    return Component.extend({
        defaults: {
            template: 'Rootways_Authorizecim/payment/authorizecim',
            cToken: null,
            captchaLoaded: false,
            generateC: 0
        },

        /**
         * @returns {exports.initialize}
         */
        initialize: function () {
            this._super();
            this.vaultEnabler = new VaultEnabler();
            this.vaultEnabler.setPaymentCode(this.getVaultCode());

            /*
            CC Formating
            this.creditCardNumber.subscribe(function (value) {
                console.log('test by vish');
                //

                var foo = value.split("-").join(""); // remove hyphens
                  if (foo.length > 0) {
                    foo = foo.match(new RegExp('.{1,4}', 'g')).join("-");
                  }
                console.log(foo);
                //this.creditCardNumber = '123456';
                jQuery('#rootways_authorizecim_option_cc_number').val('424242424');

            });
            */

            return this;    
        },

        initObservable: function () {
            this._super();
            
            if (this.isAcceptjs()) {
                this.includeAcceptjs('rwAcceptjsSandbox' , 'rwAcceptjs', this);
            }

            if (this.isCaptchaEnabled() == 1) {
                var verifyCallback = function(response) {
                    self.cToken = response;
                };
                var includeCaptchaHtml = function () {
                    if (!jQuery('.rw-google-captcha-authorizecim')[0]) {
                        setTimeout(function(){includeCaptchaHtml();}, 500);
                        return;
                    }
                    if (!self.captchaLoaded) {
                        grecaptcha.render('rw-google-captcha-authorizecim', {
                          'sitekey' : self.cSiteKey(),
                           'callback' : verifyCallback,
                        });
                        self.captchaLoaded = true;
                    }
                };
                includeCaptchaHtml();
            }

            return this;
        },
        
        getCode: function() {
            return 'rootways_authorizecim_option';
        },
        
        getData: function() {
            var data = this._super();

            this.vaultEnabler.visitAdditionalData(data);

            if (this.isAcceptjs()) {
                delete data.additional_data.cc_number;
                $.extend(
                    true,
                    data,
                    {
                        'additional_data': {
                            'data_value': $('#'+this.getCode()+'_data_value').val(),
                            'data_descriptor': $('#'+this.getCode()+'_data_descriptor').val(),
                            'captcha_string': this.cToken
                        }
                    }
                );
            } else {
                $.extend(
                    true,
                    data,
                    {
                        'additional_data': {
                            'captcha_string': this.cToken
                        }
                    }
                );
            }

            return data;                  
        },

        includeAcceptjs: function(standbox, production, self) {
            if (configAuthorizenet.environment == 'sandbox') {
                require(
                    [standbox],
                    self.initAcceptJs.bind(self)
                );
            } else {
                require(
                    [production],
                    self.initAcceptJs.bind(self)
                );
            }
        },
        
        ccLogoLocation: function() {
            return configAuthorizenet.cclogolocation;
        },

        initAcceptJs: function() {
            window[this.item.method + '_acceptjs_response_fun'] = this.transactionResponseHandler.bind(this);
            window.isReady = true;
        },

        /**
         * @returns {Bool}
         */
        isVaultEnabled: function () {
            return this.vaultEnabler.isVaultEnabled();
        },

        /**
         * @returns {String}
         */
        getVaultCode: function () {
            return configAuthorizenet.ccVaultCode;
        },

        /**
         * @returns {Bool}
         */
        isAcceptjs: function () {
            return configAuthorizenet.enableAcceptjs;
        },
        
        showNote: function () {
            if (configAuthorizenet.topNote != '' &&
                configAuthorizenet.topNote != null
               ) {
                return true;
            } else {
                return false;
            }
        },
        
        getTopNote: function () {
            return configAuthorizenet.topNote;
        },

        /**
         * @returns {Bool}
         */
        isCaptchaEnabled: function() {
            return configAuthorizenet.iscaptchaenable;
        },

        /**
         * @returns {String}
         */
        cSiteKey: function() {
            return configAuthorizenet.captchasitekey;
        },

        /**
         * @returns {String}
         */
        getClientKey: function() {
            return configAuthorizenet.apiClientKey;
        },

        /**
         * @returns {String}
         */
        getApiLoginId: function() {
            return configAuthorizenet.apiLoginId;
        },

        /**
         * @returns {Bool}
         */
        isActive: function() {
            return true;
        },

        /**
         * @returns {Bool}
         */
        isCustLoggedIn: function () {
            return (configAuthorizenet.isCustLoggedIn == false) ? false: true;
        },

        validate: function() {
            var $form = $('#' + this.getCode() + '-form');
            return $form.validation() && $form.validation('isValid');
        },

        getCcMonths: function() {
            return configAuthorizenet.ccMonths;
        },

        getCcYears: function() {
            return configAuthorizenet.ccYears;
        },

        getCcAvailableTypes: function() {
            return configAuthorizenet.availableCardTypes;
        },

        getCcMonthsValues: function() {
            return _.map(this.getCcMonths(), function(value, key) {
                return {
                    'value': key,
                    'month': value
                }
            });
        },

        getCcYearsValues: function() {
            return _.map(this.getCcYears(), function(value, key) {
                return {
                    'value': key,
                    'year': value
                }
            });
        },

        getCcAvailableTypesValues: function() {
            return _.map(this.getCcAvailableTypes(), function(value, key) {
                return {
                    'value': key,
                    'type': value
                }
            });
        },

        getCvvImageUrl: function () {
            return window.checkoutConfig.payment.ccform.cvvImageUrl[this.getCode()][0];
        },

        transactionResponseHandler: function (response) {
            if (response.messages.resultCode === 'Error') {
                this.handleResponseError(response);
            } else {
                this.handleResponseSuccess(response);
            }
        },

        handleResponseError: function (response) {
            jQuery('#rootways_authorizecim_option_data_value').val('');
            jQuery('#rootways_authorizecim_option_data_descriptor').val('');

            for (var i = 0; i < response.messages.message.length; i++) {
                alert($.mage.__('Error processing your order, Error: '+response.messages.message[i].text));
            }
        },

        handleResponseSuccess: function (response) {
            jQuery('#rootways_authorizecim_option_data_value').val(response.opaqueData.dataValue);
            jQuery('#rootways_authorizecim_option_data_descriptor').val(response.opaqueData.dataDescriptor);

            this.placeOrder();
        },
        
        getPlaceOrderDeferredObject: function () {
            return this._super()
                       .fail(this.clearAcceptJSData.bind(this));
        },

        clearAcceptJSData: function (response) {
            jQuery('#rootways_authorizecim_option_data_value').val('');
            jQuery('#rootways_authorizecim_option_data_descriptor').val('');
        },

        acceptJSCaller: function() {
            var  secureData  =  {}  ,  authData  =  {}  ,  cardData  =  {};

            // Extract the card number and expiration date.
            cardData.cardNumber  =   $('#'+this.getCode()+'_cc_number').val();
            cardData.cardCode = $('#'+this.getCode()+'_cc_cid').val();
            cardData.month  =  $('#'+this.getCode()+'_expiration').val();
            cardData.year  =  $('#'+this.getCode()+'_expiration_yr').val();
            secureData.cardData  =  cardData;

            // The Authorize.Net Client Key is used in place of the traditional Transaction Key. The Transaction Key
            // is a shared secret and must never be exposed. The Client Key is a public key suitable for use where
            // someone outside the merchant might see it.

            authData.clientKey  =  this.getClientKey();
            authData.apiLoginID  =  this.getApiLoginId();
            secureData.authData  =  authData;

            // Pass the card number and expiration date to Accept.js for submission to Authorize.Net.
            Accept.dispatchData(secureData, this.item.method + '_acceptjs_response_fun');
        },

        /*
        managePlaceOrder: function() {
            //if ($('#rootways_authorizecim_option-form').valid()) {
                if (this.isAcceptjs()) {
                    this.acceptJSCaller();
                } else {
                    this.placeOrder();
                }
           // }
        },
        */

        placeOrder: function (data, event) {
            var self = this;
            if (this.isAcceptjs() &&
                jQuery('#rootways_authorizecim_option_data_value').val() == '' &&
                jQuery('#rootways_authorizecim_option_data_descriptor').val() == ''
               ) {
                this.acceptJSCaller();
            } else {
                if (this.isAcceptjs() == 1 ||
                    self.isCaptchaEnabled() != 3
                   ) {
                    return self._super(data, event);
                } else {
                    if (self.generateC == 1) {
                        self.generateC = 0;
                        return self._super(data, event);
                    } else {
                        grecaptcha.ready(function() {
                            grecaptcha.execute(self.cSiteKey(), {action: 'submit'}).then(function(token) {
                                self.cToken = token;
                                self.generateOrder();
                            });
                        });
                    }
                }
            }
        },

        generateOrder: function() {
            this.generateC = 1;
            this.placeOrder();
        }
    });
});
