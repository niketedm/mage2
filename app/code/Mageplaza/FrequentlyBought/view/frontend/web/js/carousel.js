define([
    'jquery',
    'owlCarousel'
], function($) {
    return function(config, element) {
        $(element).owlCarousel(config);
    };
});