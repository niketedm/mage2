define(['jquery'], function($) {
     'use strict';
    return function () {
        $('.filter dt.filter-options-title').on('click',function(e){$(e.target).next('dd.filter-options-content').toggle();});
    }
});