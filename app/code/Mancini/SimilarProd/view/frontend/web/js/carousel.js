define([
    'jquery',
    'Mancini_SimilarProd/js/owl.carousel.min'
], function ($) {
    'use strict';

    $(".owlSP").owlCarousel({
        nav: false,
        margin: 30,
        responsive: {
            0: {
                items: 3,
                loop: false,
            },
            769: {
                items: 4,
                loop: false,
            }
        }
    });
}
);