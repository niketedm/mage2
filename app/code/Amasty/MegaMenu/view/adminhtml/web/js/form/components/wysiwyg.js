define([
    'jquery',
    'Magento_PageBuilder/js/form/element/wysiwyg'
], function ($, Wysiwyg) {
    'use strict';

    /**
     * Extend the original PageBuilder functionality
     */
    return Wysiwyg.extend({
        /**
         * Hide notice.
         *
         * @returns {Abstract} Chainable.
         */
        hideNotice: function () {
            this.notice('');

            return this;
        },

        /**
         * Show notice.
         *
         * @returns {Abstract} Chainable.
         */
        showNotice: function () {
            this.notice(this.defaultNotice);

            return this;
        }
    });
});
