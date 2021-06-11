define([
    'jquery',
    'mage/storage',
    'jquery/jquery-storageapi'
], function ($) {
    'use strict';

    var storage = $.initNamespaceStorage('synchrony-cache-storage').localStorage;

    return storage;
});
