define([
    'jquery',
    'uiComponent',
    'Synchrony_DigitalBuy/js/model/messages'
], function ($, Component, MessageList) {
    'use strict';

    return Component.extend({
        defaults: {
            messages: []
        },

        /**
         * Extends Component object by storage observable messages.
         */
        initialize: function () {
            this._super();
            this.messages = MessageList.messages;
        }
    });
});
