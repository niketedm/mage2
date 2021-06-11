define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    'use strict';
    $.widget('mage.spinlayoutwidget', {
        options: {
        },
        _create: function () {
            var self = this;
            $('#layout_view').on('change', function() {
                if ($(this).val()=='popup') {
                    $('#layout_position').closest('.admin__field').hide();
                } else {
                    $('#layout_position').closest('.admin__field').show();
                }
            });
            $('#layout_view').trigger('change');
        }
    });
    return $.mage.spinlayoutwidget;
});
