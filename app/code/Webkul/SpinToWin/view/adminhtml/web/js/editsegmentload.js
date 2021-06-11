define([
    "jquery",
    'mage/translate'
], function ($, $t) {
    'use strict';
    $.widget('mage.spineditsegmentloadwidget', {
        options: {
        },
        _create: function () {
            var button ='';
            button += '<button type="button" id="update-spineditsegmentform" class="action-default save spin-save">';
            button += '<span>'+$t("Save Segment")+'</span>';
            button += '<span class="admin__page-nav-item-messages">';
            button += '<span class="admin__page-nav-item-message _changed">';
            button += '<span class="admin__page-nav-item-message-icon"></span>';
            button += '<span class="admin__page-nav-item-message-tooltip">'+$t('Changes have been made to this section that have not been saved.')+'</span>';
            button += '</span>';
            button += '</span>';
            button += '</button>';
            $('div.ui-tabs-panel[aria-labelledby="spin_tabs_addsegment"]').append(button);
            $('body').trigger("spinSegmentEditLoaded");
            $('body').find('#segmentrule_type').trigger('change')
        }
    });
    return $.mage.spineditsegmentloadwidget;
});
