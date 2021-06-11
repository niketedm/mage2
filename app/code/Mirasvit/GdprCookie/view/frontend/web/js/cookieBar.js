define([
    'jquery',
    'underscore',
    'domReady!',
    'mage/cookies'
], function ($, _) {
    'use strict';

    $.widget('mirasvit.cookieBar', {
        $bar:           $('#gdprCookieBar'),
        $btnAllow:      $('.consent-button'),
        $modalSettings: $('.cookie-modal-settings'),
        $modalSave:     $('.cookie-modal-settings .mst-gdpr__cookie-settings--success-btn'),
        $agreeButton:   $('[data-trigger-settings="agree"]'),

        $overlay: $('#gdprCookieBarOverlay'),

        options: {
            allowUrl:          '',
            groupCookieName:   '',
            consentCookieName: '',
            lockScreen:        false
        },

        _create: function () {
            if (this.isAllowed()) {
                this.hide();
            } else {
                this.show();
            }

            $('button', this.$btnAllow).on(
                'click',
                this.handleAllow.bind(this)
            );

            $(this.$modalSave).on(
                'click',
                this.handleAllow.bind(this)
            );

            $(this.$agreeButton).on(
                'click',
                this.handleAllow.bind(this)
            );
        },

        isAllowed: function () {
            return !!$.mage.cookies.get(this.options.groupCookieName) && !!$.mage.cookies.get(this.options.consentCookieName);
        },

        handleAllow: function () {
            this.$bar.addClass('_loading');
            
            var data = {group_ids: []};
            _.each(jQuery('.cookie-group-container .checkbox:checked'), function (item) {
                data.group_ids.push($(item).data('group-id'));
            }.bind(this));

            $.post(
                this.options.allowUrl,
                data
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
