define([
    'Magento_Ui/js/grid/columns/column'
], function (Column, utils) {
    'use strict';

    return Column.extend({
        defaults: {
            bodyTmpl: 'Mirasvit_GdprCookie/ui/column/cookie'
        },

        getUrl: function (record) {
            return record[this.index]['cookie_id'];
        },

        getLabel: function (record) {
            return record[this.index]['cookie_id'];
        }
    });
});
