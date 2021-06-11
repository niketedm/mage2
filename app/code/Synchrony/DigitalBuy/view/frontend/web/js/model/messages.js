define([
    'ko',
    'uiClass',
    'underscore'
], function (ko, Class, _) {
    'use strict';

    var Messages = Class.extend({
        /** @inheritdoc */
        initialize: function () {
            this._super()
                .initObservable();

            return this;
        },

        /** @inheritdoc */
        initObservable: function () {
            this.messages = ko.observableArray([]);
            return this;
        },

        add: function (text, type) {
            this.messages.push({text: text, type: type});
            return this;
        },

        addSuccessMessage: function (text) {
            this.add(text, 'success');
            return this;
        },

        addNoticeMessage: function (text) {
            this.add(text, 'notice');
            return this;
        },

        addErrorMessage: function (text) {
            this.add(text, 'error');
            return this;
        },

        clear: function () {
            this.messages.removeAll();
            return this;
        }
    });

    return new Messages();
});
