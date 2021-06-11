define([
    "jquery",
    'mage/translate',
    'Magento_Ui/js/modal/alert',
], function ($, $t, alert) {
    'use strict';
    $.widget('mage.spineditsegmentwidget', {
        options: {
        },
        _create: function () {
            var self = this;

            $('body').on('spinSegmentEditLoaded', function() {
                $('a#spin_tabs_addsegment').attr('href',self.options.edit);
            });
            $('body').on('spinSegmentIndexLoaded', function() {
                $('a#spin_tabs_segments').attr('href',self.options.get);
            });
            $('body').on('change', '#segmentrule_type', function() {
                if(parseInt($(this).val())) {
                    // $('#segmentrule_value').closest('.admin__field.field').show();
                    $('#segmentrule_conditions_fieldset').show();
                    $('#segmentrule_action_fieldset').show();
                    // $('#segmentrule_value').addClass('required-entry');
                    $('#segmentrule_simple_action').addClass('required-entry');
                    $('#segmentrule_discount_amount').addClass('required-entry');
                    $('#segmentrule_stop_rules_processing').addClass('required-entry');
                } else {
                    // $('#segmentrule_value').closest('.admin__field.field').hide();
                    $('#segmentrule_conditions_fieldset').hide();
                    $('#segmentrule_action_fieldset').hide();
                    // $('#segmentrule_value').removeClass('required-entry');
                    $('#segmentrule_simple_action').removeClass('required-entry');
                    $('#segmentrule_discount_amount').removeClass('required-entry');
                    $('#segmentrule_stop_rules_processing').removeClass('required-entry');
                }
            });
            $('body').on('click', '#update-spineditsegmentform', function() {
                if (!validateSegment()) {
                    var formData = $('body').find('div.ui-tabs-panel[aria-labelledby="spin_tabs_addsegment"]').find('select, textarea, input').serialize();
                    $.ajax({
                        type: "POST",
                        url: self.options.save,
                        data: formData,
                        dataType: "json",
                        beforeSend: function () {
                            $('body').trigger('processStart');
                        },
                        success: function(response)
                        {
                            if (parseInt(response.success)) {
                                $(self).find('.admin__page-nav-item-message').addClass('_changed');
                                $('body').find('a#spin_tabs_segments').trigger('click');
                                alert({
                                    title: $t('Success'),
                                    content: $t(response.message)
                                });
                                if (response.data !== undefined && response.data) {
                                    $(form).trigger("spinAjaxComplete", [response.data]);
                                }
                            } else {
                                alert({
                                    title: $t('Error'),
                                    content: $t(response.message)
                                });
                            }
                            $('body').trigger('processStop');
                        },
                        error: function (response) {
                            $('body').trigger('processStop');
                        }
                    });
                }
            });
            function validateSegment() {
                var errorflag = false;
                var toValidate = ['#segmentrule_label', '#segmentrule_heading', '#segmentrule_description', '#segmentrule_limits', '#segmentrule_gravity', '#segmentrule_position', '#segmentrule_type', '#segmentrule_simple_action', '#segmentrule_discount_amount', '#segmentrule_discount_qty', '#segmentrule_discount_step', '#segmentrule_stop_rules_processing'];
                $.each(toValidate, function (ind, value) { 
                    if(!$.validator.validateElement(value)) {
                        errorflag = true;
                        $(value).addClass('mage-error');
                    } else {
                        $(value).removeClass('mage-error');
                    }
                });

                return errorflag;
            }
            $('body').on('click', '.spin-segment-edit-action', function (e) {
                var segmentlink = $('a#spin_tabs_addsegment').attr('href');
                segmentlink += 'id/'+$(this).data('id');
                $('a#spin_tabs_addsegment').attr('href', segmentlink);
                $('a#spin_tabs_addsegment').trigger('click');
            });
        }
    });
    return $.mage.spineditsegmentwidget;
});
