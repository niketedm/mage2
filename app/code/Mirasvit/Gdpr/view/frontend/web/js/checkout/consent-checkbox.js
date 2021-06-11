define([
    'ko',
    'jquery',
    'uiComponent',
    'Magento_Checkout/js/model/payment/additional-validators',
    'mage/translate',
    'mage/validation'
], function (ko, $, Component, additionalValidators) {
    'use strict';
    
    return Component.extend({
        config:          window.checkoutConfig.gdpr,
        elementSelector: '#co-payment-form #is_consented',
        
        initialize: function () {
            this._super();
            
            additionalValidators.registerValidator({
                validate: function () {
                    if (!this.isVisible()) {
                        return true;
                    }
                    
                    var $form = $('#co-payment-form');
                    
                    $form.validate({
                        ignore:         $(':not(' + this.elementSelector + ')', $form),
                        meta:           'validate',
                        errorElement:   'div',
                        errorClass:     'mage-error',
                        errorPlacement: function (error, element) {
                            element.parent().append(error)
                        }
                    });
                    
                    return $form.valid();
                }.bind(this)
            });
            
            return this;
        },
        
        isVisible: function () {
            return this.config.isEnabled;
        },
        
        getText: function () {
            return $.mage.__(this.config.text)
        }
    });
});
