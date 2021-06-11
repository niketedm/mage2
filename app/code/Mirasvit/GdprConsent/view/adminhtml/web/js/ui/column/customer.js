define([
    'Magento_Ui/js/grid/columns/column'
], function (Column, utils) {
    'use strict';
    
    return Column.extend({
        defaults: {
            bodyTmpl: 'Mirasvit_Gdpr/ui/column/customer'
        },
        
        getUrl: function (record) {
            return record[this.index]['url'];
        },
        
        getLabel: function (record) {
            return record[this.index]['label'];
        }
    });
});
