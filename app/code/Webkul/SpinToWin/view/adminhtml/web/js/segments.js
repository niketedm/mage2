define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    'use strict';
    $.widget('mage.spinsegmentsloadwidget', {
        options: {
        },
        _create: function () {
            $('body').trigger("spinSegmentIndexLoaded");
        }
    });
    return $.mage.spinsegmentsloadwidget;
});
