define([
    'jquery',
    'Mancini_Sleepworld/js/owl.carousel.min'
], function ($) {
    'use strict';


    /*    Carousel for Featured Categories*/

    $(".owlFC").owlCarousel({
        loop: false,
        items: 6,
        nav: false,
        responsive: {
            0: {
                items: 2,
                nav: false,
                loop: false,
                stagePadding: 50,
                margin: 20
            },
            600: {
                items: 3,
                nav: false,
                loop: false
            },
            1000: {
                items: 6,
                nav: false,
                loop: false
            }
        }

    });


    /*Carousel for Hero Banner Slider*/

    $(".owlHBS").owlCarousel({
        items: 1,
        nav: false,
        loop: true,
        autoplay: true
    });


    /*		Carousel for Shop other deals*/

    $(".owlSOD").owlCarousel({
        items: 1,
        nav: false,
        loop: false,
    });


    /*		Carousel for Guide and Resources*/

    $(".owlGR").owlCarousel({
        items: 1,
        nav: false,
        loop: false,
    });


    /*Carousel for Our top brands*/

    $(".owlOTB").owlCarousel({
        loop: false,
        items: 6,
        nav: false,
        onInitialized: otbSLideCount,
        onTranslated: updateDisplay,
        responsive: {
            0: {
                items: 2,
                nav: false,
                loop: false,
                stagePadding: 60,
                margin: 20
            },
            600: {
                items: 3,
                nav: false,
                loop: false
            },
            1000: {
                items: 6,
                nav: false,
                loop: false
            }
        }

    });

    function otbSLideCount() {
        var className = 'owlOTB';
        count(className);
    }

    /*Carousel for Our top brands*/

    $(".owlMatSize").owlCarousel({
        loop: false,
        items: 6,
        nav: false,
        onInitialized: owlMatSLideCount,
        onTranslated: updateDisplay,
        responsive: {
            0: {
                items: 2,
                nav: false,
                loop: false,
                stagePadding: 30,
                margin: 20
            },
            600: {
                items: 3,
                nav: false,
                loop: false
            },
            1000: {
                items: 6,
                nav: false,
                loop: false
            }
        }

    });

    function owlMatSLideCount() {
        var className = 'owlMatSize';
        count(className);
    }

    /*Carousel for MDeals*/
    /* This carousel is reused in Mattress Brand Template Page - Hussain */

    $(".owlMC").owlCarousel({
        loop: false,
        // margin: 30,
        onInitialized: mdSLideCount,
        onTranslated: updateDisplay,
        responsive: {
            0: {
                items: 1,
                nav: false,
                loop: false
            },
            600: {
                items: 2,
                nav: false,
                loop: false,
                margin: 25
            },
            1000: {
                items: 3,
                nav: false,
                loop: false,
                margin: 25
            }
        }
    });

    function mdSLideCount() {
        var className = 'owlMC';
        count(className);
    }

    /** What our customers says */

    $(".owlCS").owlCarousel({
        loop: false,
        responsiveClass: true,
        onInitialized: wocsSLideCount,
        onTranslated: updateDisplay,
        responsive: {
            0: {
                items: 1,
                nav: false,
                stagePadding: 50,
                margin: 20
            },
            600: {
                items: 2,
                nav: false,
                loop: true,
                margin: 25
            },
            1000: {
                items: 4,
                nav: false,
                loop: true,
                margin: 25,
            }
        }
    });

    function wocsSLideCount() {
        var className = 'owlCS';
        count(className);
    }

    /** Blog Category Slider */
    $('.categories-slider').owlCarousel({
        loop: false,
        // margin:5,
        nav: true,
        onInitialized: blogCategorySlideCount,
        onTranslated: updateDisplay,
        navText: ["<img aria-label='Previous' src='../media/magefan_blog/tmp/left.png'>", "<img aria-label='Next' src='../media/magefan_blog/tmp/right.png'>"],
        // stagePadding: 50,
        dots: false,
        responsive: {
            0: {
                items: 1,
                stagePadding: 50,
                nav: false,
            },
            600: {
                items: 3
            },
            1000: {
                items: 6
            }
        }
    });

    function blogCategorySlideCount() {
        var className = 'categories-slider';
        count(className);
    }

    $('.recent-post-carousel').owlCarousel({
        loop: false,
        nav: false,
        items: 1
    });

    // Functions to handle carousel accessibility

    function count(className) {
        var count = 0;
        var totalDots = 0;
        // var className = 'owl-carousel';
        $('.' + className).each(function () {
            // Find total number of dots
            $(this).find('.owl-dot').each(function (index) {
                //Add one to index so it starts from 1
                totalDots = index + 1;
            });

            //Find each set of dots in this carousel
            $(this).find('.owl-dot').each(function (index) {
                //Add one to index so it starts from 1
                count = index + 1;
                $(this).attr('aria-label', 'slide ' + count + ' of ' + totalDots);
                $(this).attr('aria-current', false);
            });
        });

        $('.owl-dot.active').attr('aria-current', true);

        $(this).find('.owl-dot').each(function (index) {
            $(this).attr('aria-current', false);
            console.log('Value of aria current: ', $(this).attr('aria-current'));
            if($(this).attr('aria-current')) {
                console.log('Current If');
            } else {
                console.log('Current else');
            }
        });

        $('.owl-carousel').find('.owl-item').attr('aria-hidden', 'true'); // let screen readers know an item is hidden
        $('.owl-carousel').find('.owl-item.active').attr('aria-hidden', 'false'); // let screen readers know an item is active

        // $('.owl-carousel').find('.owl-item a').attr('tabindex', -1);
        // $('.owl-carousel').find('.owl-item').attr('tabindex', -1);
        // $('.owl-carousel').find('.owl-item.active').attr('tabindex', 0);
    }

    function updateDisplay() {

        $('.owl-dot').attr('aria-current', false);
        $('.owl-dot.active').attr('aria-current', true);

        $('.owl-carousel').find('.owl-item').attr('aria-hidden', 'true'); // let screen readers know an item is hidden
        $('.owl-carousel').find('.owl-item.active').attr('aria-hidden', 'false'); // let screen readers know an item is active

        // $('.owl-carousel').find('.owl-item').attr('tabindex', -1);
        // $('.owl-carousel').find('.owl-item.active').attr('tabindex', 0);
    }

});


