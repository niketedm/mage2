define([
    "jquery",
    'mage/translate',
    "mage/calendar"
], function ($, $t) {
    'use strict';
    $.widget('mage.spinformwidget', {
        options: {
        },
        _create: function () {
            var self = this;
            setTimeout(function() {
                $('.spin-content-editform-edit input:not([type=file]), .spin-content-editform-edit select, .spin-content-editform-edit textarea, .spin-content-resultform-edit input:not([type=file]), .spin-content-resultform-edit select, .spin-content-resultform-edit textarea').trigger('change');
                $('form.spin-form').find('.spin-save .admin__page-nav-item-message').addClass('_changed');
            }, 500);
            $('#edit_cname_status, #edit_cemail_status, #edit_show_progress').on('change', function() {
                var elemId = $(this).data('spin-bind');
                if(parseInt($(this).val())) {
                    $('.'+elemId).show();
                } else {
                    $('.'+elemId).hide();
                }
            });
            $('#edit_cname_required, #edit_cemail_required').on('change', function() {
                var elemId = $(this).data('spin-bind');
                if(parseInt($(this).val())) {
                    $('.'+elemId).addClass('required');
                } else {
                    $('.'+elemId).removeClass('required');
                }
            });
            $('#result_coupon_button_background_color').on('change', function () {  
                $('.spin-coupon-code').css('border-color', $(this).val());
                $('.spin-coupon-code-copy').css('border-color', $(this).val());
            });
            
            $('#spineditformedit').on('spinAjaxComplete', function(e, data) {
                $('#edit_brand_logo_hidden').val(data.logo);
            });

            $('#spinresultformedit').on('spinAjaxComplete', function(e, data) {
                $('#result_brand_logo_hidden').val(data.logo);
            });

            $('body').on('click', '.spin-coupon-code-copy', function() {
                var couponCode = $(this).prev('.spin-coupon-code');
                couponCode.select();
                document.execCommand("copy");
            });

            $('body').on('change', '#edit_show_progress', function() {
                if (parseInt($(this).val())) {
                    $('#edit_progress_percent').closest('.admin__field').show();
                    $('#edit_progress_percent').addClass('required-entry');
                    $('#edit_progress_label').closest('.admin__field').show();
                    $('#edit_progress_label').addClass('required-entry');
                } else {
                    $('#edit_progress_percent').closest('.admin__field').hide();
                    $('#edit_progress_percent').removeClass('required-entry');
                    $('#edit_progress_label').closest('.admin__field').hide();
                    $('#edit_progress_label').removeClass('required-entry');
                }
            });
            $('body').on('change', '#edit_progress_percent', function() {
                $('.spin-progress-bar-progress').css('width', $(this).val()+'%');
            });
        }
    });
    return $.mage.spinformwidget;
});
