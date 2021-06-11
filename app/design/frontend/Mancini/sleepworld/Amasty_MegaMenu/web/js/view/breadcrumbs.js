define([
    'jquery',
    'Magento_Ui/js/modal/modal'
], function ($) {
    'use strict';

    return function (breadcrumbs) {
        $.widget('mage.breadcrumbs', breadcrumbs, {

            /**
             * Returns category menu item.
             * Override the Magento method since the menuItem can contain several items
             *
             * @param {jQuery|null} menuItem
             * @returns {Object}
             * @private
             */
            _getCategoryCrumb: function (menuItem) {
                return this._super($(menuItem).first());
            }
        });

        return $.mage.breadcrumbs;
    }
});
