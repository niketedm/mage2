define([
    'jquery',
    'Mancini_Featurehighlights/js/owl.carousel.min'
], function ($) {
    'use strict';


    /*    Carousel for Featured highlights*/

    $(".owlFH").owlCarousel({
        nav: false,
        margin: 20,
        responsive: {
            0: {
                items: 2,
                loop: false,
            },
            426: {
                items: 3,
                loop: false,
            }
        }

    });
}
);

