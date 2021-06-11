define([
    'jquery',
    'underscore',
    'domReady!',
    'mage/cookies'
], function ($, _) {
    'use strict';
    
    $.widget('mirasvit.cookieBar', {
        $bar:     $('#gdprCookieBar'),
        $overlay: $('#gdprCookieBarOverlay'),
        
        options: {
            allowUrl:   '',
            cookieName: '',
            lockScreen: false
        },
        
        _create: function () {
            if (this.isAllowed()) {
                this.hide();
            } else {
                this.show();
            }
            
            $('button', this.$bar).on(
                'click',
                this.handleAllow.bind(this)
            );
        },
        
        isAllowed: function () {
            return !!$.mage.cookies.get(this.options.cookieName);
        },
        
        handleAllow: function () {
            this.$bar.addClass('_loading');
            
            $.post(
                this.options.allowUrl,
                {}
            ).done(function () {
                this.hide();
            }.bind(this));
        },
        
        show: function () {
            this.$bar.show();
            
            if (window.location.href.indexOf('cookie') === -1 && this.options.lockScreen) {
                this.$overlay.show();
                $('body').css('overflow', 'hidden');
            }
        },
        
        hide: function () {
            this.$bar.hide();
            this.$overlay.hide();
            $('body').css('overflow', 'auto');
        }
    });
    
    return $.mirasvit.cookieBar;
});