define([
    'Magento_Checkout/js/view/payment/default',
    'jquery',
    'mage/url',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/full-screen-loader',
    'Magento_Checkout/js/model/payment/additional-validators',
    'Magento_Ui/js/modal/alert'
], function (
       Component,
        $,
        url,
        quote,
        fullScreenLoader,
        additionalValidators,
        alert
       ) {
    'use strict';
    
    window.AuthorizeNetIFrame = {};
    var configAuthorizenet = window.checkoutConfig.payment.rootways_authorizecim_option_hosted;
    var configAuthorizenetHosted = window.checkoutConfig.payment.rootways_authorizecim_option_hosted;
    var agreementsConfig = window.checkoutConfig ? window.checkoutConfig.checkoutAgreements : {};
    var agreementsInputPath = '#rootways_authorizecim_option_hosted_wrapper div.checkout-agreements input';
    
    return Component.extend({
        defaults: {
            template: 'Rootways_Authorizecim/payment/authorizehosted',
            hostedPaymentResponseData: null,
            loadIframe: true,
            grandTotalAmount: 0
        },
        agreements: agreementsConfig.agreements,

        initialize: function () {
            this._super();

            var self = this;
            var prevAddress;
            quote.billingAddress.subscribe(function (newAddress) {

                if (!newAddress ^ !prevAddress || newAddress.getKey() !== prevAddress.getKey()) {
                    prevAddress = newAddress;
                    if (newAddress) {
                        self.loadHostedForm();
                    }
                }
            });
            this.grandTotalAmount = parseFloat(quote.totals()['base_grand_total']).toFixed(2);
            quote.totals.subscribe(function () {
                if (this.grandTotalAmount !== quote.totals()['base_grand_total']) {
                    this.grandTotalAmount = parseFloat(quote.totals()['base_grand_total']).toFixed(2);
                    self.loadHostedForm();
                }
            }.bind(this));

            return this;
        },

        initObservable: function () {
            this._super();
            this.loadHostedForm();
            this.agreementListner();
            return this;
        },
        
        getCode: function() {
            return 'rootways_authorizecim_option_hosted';
        },
        
        getData: function () {
            if (this.loadIframe) {
                this.loadHostedForm();
            }
            this.loadIframe = false;

            var data = {
                'method': this.getCode(),
                'additional_data': {
                    'payment_method_nonce': this.hostedPaymentResponseData
                }
            };
            return data;
        },
        
        agreementValidation: function() {
            if (additionalValidators.validate()) {
                $('.' + this.getCode() + '_iframe_wrapper').removeClass('agreement_missed');
            } else {
                $('.' + this.getCode() + '_iframe_wrapper').addClass('agreement_missed');
            }
        },
        
        agreementListner: function() {
            var self = this;
            
            var setAgreeListner = function () {
                if (!jQuery('#' + self.getCode() + '_wrapper .checkout-agreements')[0]) {
                    setTimeout(function(){setAgreeListner();}, 500);
                    return;
                }
                
                var isValid = true;
                $(agreementsInputPath).each(function (index, element) {
                    element.addEventListener('change', self.agreementValidation.bind(self), false);
                    if (!$.validator.validateSingleElement(element, {
                        errorElement: 'div',
                        hideError: false
                    })) {
                        isValid = false;
                    }
                });
                if (isValid == false) {
                    $('.' + self.getCode() + '_iframe_wrapper').addClass('agreement_missed');
                }
            };
            setAgreeListner();
        },

        loadHostedForm: function() {
            var self = this;

            fullScreenLoader.startLoader();
            var includeHostedForm = function () {
                if (!jQuery('.embed-responsive-item')[0]) {
                    setTimeout(function(){includeHostedForm();}, 300);
                    return;
                }
                var validateUrl = url.build('rootways_authorizecim/hostedform/index');

                var cEmail = '';
                if(quote.guestEmail) {
                    cEmail = quote.guestEmail;
                } else {
                    cEmail = window.checkoutConfig.customerData.email;
                }
                var DataArray = { "amount": self.grandTotalAmount};
                if (quote.billingAddress()) {
                    var billingAddress = quote.billingAddress();
                    DataArray['billfirstname'] = billingAddress.firstname;
                    DataArray['billlastname'] = billingAddress.lastname;
                    DataArray['billcompany'] = billingAddress.company;
                    var street = '';
                    if (billingAddress.street) {
                        street = billingAddress.street[0];
                    }
                    DataArray['billaddress'] = street;
                    DataArray['billcity'] = billingAddress.city;
                    DataArray['billstate'] = billingAddress.regionCode;
                    DataArray['billzip'] = billingAddress.postcode;
                    DataArray['billconid'] = billingAddress.countryId;
                    DataArray['billphone'] = billingAddress.telephone;
                    DataArray['email'] = cEmail;
                }
                if (quote.shippingAddress()) {
                    var shippingAddress = quote.shippingAddress();
                    DataArray['shipfirstname'] = shippingAddress.firstname;
                    DataArray['shiplastname'] = shippingAddress.lastname;
                    DataArray['shipcompany'] = shippingAddress.company;
                    var street = '';
                    if (shippingAddress.street) {
                        street = shippingAddress.street[0];
                    }
                    DataArray['shipaddress'] = street;
                    DataArray['shipcity'] = shippingAddress.city;
                    DataArray['shipstate'] = shippingAddress.regionCode;
                    DataArray['shipzip'] = shippingAddress.postcode;
                    DataArray['shipconid'] = shippingAddress.countryId;
                }

                $.ajax({
                    url : validateUrl,
                    data: DataArray,
                    type: "GET",
                    success: function(data)
                    {
                        $('#token').val(data).change();
                        $("#add_payment").show();
                        $("#send_token").attr({ "action": self.getHostedGatewayUrl(), "target": "add_payment" }).submit();
                        $(window).scrollTop($('#add_payment').offset().top - 50);
                        $('#add_payment').on('load', function () {
                            fullScreenLoader.stopLoader();
                        });
                    }
                });
                AuthorizeNetIFrame.onReceiveCommunication = self.hostedResponseHandler.bind(self);
            };
            includeHostedForm();
        },

        parseQueryString: function(str) {
            var vars = [];
            var arr = str.split('&');
            var pair;
            for (var i = 0; i < arr.length; i++) {
                pair = arr[i].split('=');
                vars[pair[0]] = unescape(pair[1]);
            }
            return vars;
        },

        hostedResponseHandler: function (querystr) {
            var params = this.parseQueryString(querystr);
            switch (params["action"]) {
                case "successfulSave":
                    break;
                case "cancel":
                    location.reload();
                    break;
                case "resizeWindow":
                    var w = parseInt(params["width"]);
                    var h = parseInt(params["height"]);
                    var ifrm = document.getElementById("add_payment");
                    ifrm.style.width = w.toString() + "px";
                    ifrm.style.height = h.toString() + "px";
                    break;
                case "transactResponse":
                    //console.log('vish done'+params);
                    //this.hostedPaymentResponseData = params["response"];
                    var responseData = querystr.split('response=');
                    this.hostedPaymentResponseData = responseData[1];
                    
                    this.placeOrder();
                }
        },
        
        showNote: function () {
            if (configAuthorizenetHosted.topNote != '' &&
                configAuthorizenetHosted.topNote != null
               ) {
                return true;
            } else {
                return false;
            }
        },
        
        getTopNote: function () {
            return configAuthorizenetHosted.topNote;
        },

        /**
         * @returns {String}
         */
        getHostedGatewayUrl: function() {
            return configAuthorizenetHosted.hostedGatewayUrl;
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
        }
    });
});
