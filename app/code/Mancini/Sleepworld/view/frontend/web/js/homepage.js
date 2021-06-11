define([
    'jquery'
], function ($) {
    'use strict';

    // $(".read-more").click(function () {
    //     $(".amscontent").toggleClass("show-more");
    //     var x = document.getElementById("read-more");
    //     x.style.display = "none";
    // });

    $(".read-more").click(function () {
        $(".about-mancini-text").toggleClass("show-more");
        var x = document.getElementById("read-more");
        // console.log("Read More/Less", $("#read-more").text());
        if ($("#read-more").text() == "Read More") {
            x.innerHTML = "Read Less";
        } else {
            x.innerHTML = "Read More";
        }

        // x.style.display = "none";
    });

    // Find your perfect sleep
    var isDivOneExpanded = false;

    // function onClickTransition() {
    $("#child2").click(function () {
        console.log("On click triggered");
        $('#child1').addClass('child1-transition');
        $('#child2').addClass('child2-transition');
        $('#child1').addClass('fs-img-one-overlay');
        $('#child2').removeClass('fs-img-one-overlay');
        // $('#child1 #child-one-box').removeClass('child-one-box');
        $('#child1').removeClass('child1-transition-out');
        $('#child2').removeClass('child2-transition-out');

        if (isDivOneExpanded !== true) {
            var x = document.getElementById('child-one-box');
            var y = document.getElementById('child-two-box');
            var z = document.getElementById('child2-side-section');
            var l = document.getElementById('child1-side-section');
            if (x.style.display === "none") {
                x.style.display = "block";
                y.style.display = "none";
                z.style.display = "block";
                l.style.display = "none";
            } else {
                x.style.display = "none";
                y.style.display = "block";
                z.style.display = "none";
                l.style.display = "block";
            }

            // var y = document.getElementById('shortData');
            // if (y.style.display === "none") {
            //     y.style.display = "block";
            // } else {
            //     y.style.display = "none";
            // }
        }

        isDivOneExpanded = true;
    });

    // function onClickTransition2() {
    $("#child1").click(function () {
        console.log("On click2 triggered");
        $('#child1').addClass('child1-transition-out');
        $('#child2').addClass('child2-transition-out');
        // $('#child2').addClass('fs-img-two-overlay');
        $('#child1').removeClass('fs-img-one-overlay');
        $('#child2').addClass('fs-img-one-overlay');
        $('#child1').removeClass('child1-transition');
        $('#child2').removeClass('child2-transition');

        if (isDivOneExpanded) {
            var x = document.getElementById('child-one-box');
            var y = document.getElementById('child-two-box');
            var z = document.getElementById('child2-side-section');
            var l = document.getElementById('child1-side-section');
            if (x.style.display === "none") {
                x.style.display = "block";
                y.style.display = "none";
                z.style.display = "block";
                l.style.display = "none";
            } else {
                x.style.display = "none";
                y.style.display = "block";
                z.style.display = "none";
                l.style.display = "block";
            }

            // var y = document.getElementById('shortData');
            // if (y.style.display === "none") {
            //     y.style.display = "block";
            // } else {
            //     y.style.display = "none";
            // }
        }

        isDivOneExpanded = false;
    });

    $("#sec-one-click-link").click(function () {
        console.log("Click One");
        // $('#child1').addClass('fs-img-one-overlay');
        // $('#child2').removeClass('fs-img-one-overlay');

        var secOneOverlay = document.getElementById('header-overlay');
        secOneOverlay.style.display = 'none';

        var bodySecOne = document.getElementById('body-sec-one');
        bodySecOne.style.display = 'none';
        var bodySecTwo = document.getElementById('body-sec-two');
        bodySecTwo.style.display = 'block';

        var footerOverlay = document.getElementById('footer-overlay');
        footerOverlay.style.display = 'block';
    });

    $("#footer-overlay").click(function () {
        var secOneOverlay = document.getElementById('header-overlay');
        secOneOverlay.style.display = 'block';

        var bodySecOne = document.getElementById('body-sec-one');
        bodySecOne.style.display = 'block';
        var bodySecTwo = document.getElementById('body-sec-two');
        bodySecTwo.style.display = 'none';

        var footerOverlay = document.getElementById('footer-overlay');
        footerOverlay.style.display = 'none';
    });

    $("#find-your-sleep").click(function () {
        window.open('/find-your-bed');
    });

    $("#bed-and-mattress").click(function () {
        window.open('/sleep-tips');
    });

    require(['jquery'], function ($) {
        $(document).ready(function () {
            $(".div-to-anchor-tag").click(function () {
                console.log("Js clickd");
                window.location.href = $(this).find('a').attr("href");
            });
        });
    });

    // $(document).ready(function () {
    //     var timer = setInterval(function () {
    //         console.log("In Timeout: " + $('.home-page-hero-banner-slider .slick-dots').length);
    //         if ($('.home-page-hero-banner-slider .slick-dots').length > 0) {
    //             $('.home-page-hero-banner-slider .slick-dots').append('<button id="playpause">Pause</button>');
    //             clearInterval(timer);
    //         }
    //     }, 2000);
    // });

    // $(document).ready('#clickme').click(function () {
    //     console.log('Slick Play value: ', $('.home-page-hero-banner-slider .pagebuilder-slider').slick('autoplay'));
    //     let currValue = $('.home-page-hero-banner-slider .slick-dots #playpause').text();
    //     if (currValue === 'Pause') {
    //         $('.home-page-hero-banner-slider .slick-dots #playpause').text('Play');
    //         $('.home-page-hero-banner-slider .pagebuilder-slider').slick('slickPause');
    //     }
    //     if (currValue === 'Play') {
    //         $('.home-page-hero-banner-slider .slick-dots #playpause').text('Pause');
    //         $('.home-page-hero-banner-slider .pagebuilder-slider').slick('slickPlay');
    //     }
    // });

    // Updated code for play and pause on banner

    $(document).ready(function () {
        var timer = setInterval(function () {
            console.log("In Timeout: " + $('.home-page-hero-banner-slider .slick-dots').length);
            if ($('.home-page-hero-banner-slider .slick-dots').length > 0) {
                // $('.home-page-hero-banner-slider .slick-dots').append('<button id="playpause">Pause</button><button id="playpause1">Play</button>');
                $('.home-page-hero-banner-slider .slick-dots').append('<button aria-label="Pause" id="home-pause"></button><button aria-label="Play" style="display: none;" id="home-play"></button>');
                clearInterval(timer);
            }
        }, 2000);

        // The below line of code is to remove alt tag from all the product carousels.
        $('.product-widget-caraousel .product-image-photo').attr('alt', '');
        $('.catalogs-products .product-image-photo').attr('alt', '');
        $('.sort-desc').attr('tabindex', '-1');
        $('.loader').attr('aria-live', 'assertive');
        

        //Code for Replacing flash sale with icon
        var list = $('.ammenu-items .ammenu-item:last-child .category .sub-types li');
            console.log(list);
            var i = 0;
            for ( i= 0; i < list.length; i++) {
            if(list[i].innerText.toLowerCase().includes("flash")) {
                // console.log('List style: ', list[i].style.display);
                if(list[i].style.display !== 'none') {
                    $('.ammenu-items .ammenu-item:last-child > a')[0].classList.add('flash');
                }
            }
        }

        // const flashSubMenuLast = document.querySelector('.ammenu-items .ammenu-item:last-child .ammenu-submenu .category li:last-child');
        // flashSubMenuLast.style.display
        

    });

    $(document).ready('#clickme').click(function () {

        let clickedId = document.activeElement.id;
        if (clickedId === 'home-play') {
            // alert('Play clicked');
            document.getElementById('home-play').style.display = 'none';
            document.getElementById('home-pause').style.display = 'inline-block';
            $('.home-page-hero-banner-slider .pagebuilder-slider').slick('slickPlay');
        } else if (clickedId === 'home-pause') {
            // alert('Pause clicked');
            document.getElementById('home-play').style.display = 'inline-block';
            document.getElementById('home-pause').style.display = 'none';
            $('.home-page-hero-banner-slider .pagebuilder-slider').slick('slickPause');
        }
    });

    // function isFlashSale() {
    //     var list = $('.ammenu-items .ammenu-item:last-child .category .sub-types li');
    //     console.log(list);
    //     var i = 0;
    //     for ( i= 0; i < list.length; i++) {
    //         if(list[i].innerText.toLowerCase().includes("flash")) {
    //             console.log('List style: ', list[i].style.display);
    //             if(list[i].style.display !== 'none') {
    //                 $('.ammenu-items .ammenu-item:last-child > a')[0].classList.add('flash');
    //             }
    //         }
    //     }
    // }

//     // Js for USP in About us page expand and collapse.

//     var isExpanded = false;
//    function addClass() {
//            var addId=document.getElementsByClassName('usp-info');
//            for (i = 0; i < addId.length; i++){
//              addId[i].setAttribute('id','usp-info-'+(i+1));
//            }
//         }
//    window.onload = addClass;
//    function onExpandClick(n) {
//         var element = document.getElementById('usp-info-'+ n);
//         if(isExpanded == false) {
//             element.classList.add('show-card');
//             isExpanded = true;
//             document.getElementById('expand-card-'+ n).classList.add('coll-hidden');
//             document.getElementById('collapse-card-'+ n).classList.remove('coll-hidden');
//         } else {
//             element.classList.remove('show-card');
//             isExpanded = false;
//             document.getElementById('expand-card-'+ n).classList.remove('coll-hidden');
//             document.getElementById('collapse-card-'+ n).classList.add('coll-hidden');
//         }
//     }


       // $(document).ready(function() {
    //     function isInViewport(el) {
    //         const rect = el.getBoundingClientRect();
    //         console.log('Share cordinates: ', rect);
    //         return (
    //             rect.top >= 0 &&
    //             rect.left >= 0 &&
    //             // rect.bottom >= 0 &&
    //             rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    //     //     rect.top >= 0 &&
    //     // rect.left >= 0 &&
    //     // rect.bottom <= (window.innerHeight || document.documentElement.clientHeight) &&
    //     // rect.right <= (window.innerWidth || document.documentElement.clientWidth)
    //         );
    //     }


    //     const box = document.querySelector('.page-main');
    //     //var x = document.querySelector('.filter-button');
    //     var mybutton = document.getElementById("social-icons");


    //     document.addEventListener('scroll', function() {

    //         const messageText = isInViewport(box) ?
    //             mybutton.style.display = "none" :
    //             mybutton.style.display = "grid";
    //     }, {
    //         passive: true
    //     });



    // });

    function stick_social() {
        var window_top = $(window).scrollTop();
        if (window_top > 700) {
            $('.addthis_toolbox').addClass('stick');
        } else {
            $('.addthis_toolbox').removeClass('stick');
        }
    }
    
    $(function () {
        $(window).scroll(stick_social);
        stick_social();
    });


}
);