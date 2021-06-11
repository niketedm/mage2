define([
    'jquery'
], function ($) {
    'use strict';
    
    return function () {
        $('[data-show][data-hide]').on('click', function (e) {
            var $el = $(e.currentTarget);
            var toShow = $el.attr('data-show');
            var toHide = $el.attr('data-hide');
            
            $("#" + toShow).show();
            $("#" + toHide).hide();
        })
    }
});