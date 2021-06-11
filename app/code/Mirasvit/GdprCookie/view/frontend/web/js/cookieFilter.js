define([
    'jquery',
    'underscore',
    'domReady!',
    'mage/cookies'
], function ($, _) {
    'use strict';

    $.widget('mirasvit.cookieFilter', {
        options: {
            groups:            {},
            groupCookieName:   '',
            consentCookieName: ''
        },

        _create: function () {
            if (!this.isConsent()) {
                return;
            }
    
            setInterval(function () {
                var allowedGroups = this.getAllowedGroups();
                for(var groupId in this.options.groups) {
                    if (allowedGroups.indexOf(groupId) == -1) {
                        _.each(this.options.groups[groupId], function(cookieCode) {
                            this.deleteCookie(cookieCode);
                        }.bind(this));
                    }
                }
            }.bind(this), 5000)
        },
    
        isConsent: function () {
            return !!$.mage.cookies.get(this.options.consentCookieName);
        },
    
        getAllowedGroups: function () {
            return $.mage.cookies.get(this.options.groupCookieName).split(',');
        },
    
        deleteCookie: function (cname) {
            var d = new Date();
            
            // set time in the past (1day)
            d.setTime(d.getTime() - (1 * 24 * 60 * 60 * 1000));
            
            var expires = "expires="+d.toUTCString();
            
            document.cookie = cname + "=;" + expires + ";path=/";
        }
    });

    return $.mirasvit.cookieFilter;
});
