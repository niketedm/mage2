define([
    'jquery',
    'mage/url',
    'Mancini_SimilarProd/js/owl.carousel.min'
], function ($, url) {
    'use strict';

    $(".owlPR").owlCarousel({
        nav: false,
        margin: 30,
        items: 6,
        responsive: {
            0: {
                items: 3,
                loop: false,
                margin: 50
            },
            768: {
                items: 6,
                loop: false,
                margin: 30,
                nav: false
            }
        }
    });

    var owl = $('.carousel-1');
    owl.owlCarousel({
        items: 4,
        nav: false,
        margin: 50,
        responsive: {
            0: {
                items: 3,
                margin: 50,
                loop: false,
            },
            768: {
                items: 3,
                loop: false,
                margin: 10
            },
            1000: {
                items: 4,
                margin: 30,
                nav: false,
                loop: false
            }
        }
    });

    $("[data-media]").on("click", function (e) {
        e.preventDefault();
        var $this = $(this);
        var videoUrl = $this.attr("data-media");
        var popup = $this.attr("href");
        var $popupIframe = $(popup).find("iframe");
        $popupIframe.attr("src", videoUrl);
        $('.popup').addClass("show-popup");
    });

    $(".carousel-review-image").on("click", function (e) { 
        e.preventDefault();
        var $this = $(this);
        var imageUrl = '<img src="'+$this.attr("src")+'" width="300" height="300px"/>'
        $("#img-popup").html(imageUrl);
        $('.popup').addClass("show-popup");
    });

    $(".popup").on("click", function (e) {
        e.preventDefault();
        e.stopPropagation();

        $(".popup").removeClass("show-popup");
    });
    
    $(".popup > iframe").on("click", function (e) {
        e.stopPropagation();
    });

    $(".reviewpaging").on("click", function () {
        var currentpage = $(this).attr('id');
        var prdsku      = $(this).attr("data-prd");
        getPagination(currentpage, prdsku);
    });

    $(".left").on("click", function () {
        var currentpage = $(this).attr('id');
        var prdsku      = $(this).attr("data-prd");
        getPagination(currentpage, prdsku);
    });

    $(".right").on("click", function () {
        var currentpage = $(this).attr('id');
        var prdsku      = $(this).attr("data-prd");
        getPagination(currentpage, prdsku);
    });

    function getPagination(currentpage, prdsku) {
        var maxCountofA = $("a.reviewpaging").length;
        var maxvalue = parseInt(maxCountofA) * 5;

        var currentid = currentpage.split(/[\s-]+/);
        var pageid = currentid[currentid.length-1]

        var prev = parseInt(pageid) - 5;
        var next = parseInt(pageid) + 5;
       
        url.setBaseUrl(BASE_URL);
        var link = url.build('preview/reviewpage');

        $.ajax({
            context: '#reviewlisting',
            showLoader: true,
            url: link,
            data: { currentpage: pageid , prdsku:prdsku},
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            $(".reviewpaging").removeClass("active");
            addActive(prev, next, maxvalue);
            $("#page-" + pageid).addClass("active");

            $("#reviewlisting").html(data.output);
            var owl = $('.carousel-1');
            owl.owlCarousel({
                items: 4,
                nav: true,
                margin: 50,
                responsive: {
                    0: {
                        items: 3,
                        loop: false,
                    },
                    768: {
                        items: 4,
                        loop: false,
                    }
                }
            });
        });
    }

    function addActive(prev, next, maxvalue) {
        if ((next < maxvalue) &&(prev > 0)) {
            $(".left").attr('id', "left-"+prev);
            $(".left").css("display", "block");
            $(".right").css("display", "block");
            $(".right").attr('id', "right-"+next);
        }
        if ((next < maxvalue) &&(prev < 0)) {
            if(prev < 0){
                $(".left").attr('id', "left-0");
            } else{
                $(".left").attr('id', "left-"+prev);
            }
            $(".left").css("display", "none");
            $(".right").css("display", "block");
            $(".right").attr('id', "right-"+next);
        }
        if ((next >= maxvalue) &&(prev > 0)) {
            $(".left").attr('id',"left-"+ prev);
            $(".left").css("display", "block");
            $(".right").css("display", "none");
        }
        if ((next >= maxvalue) &&(prev <= 0)) {
            if(prev < 0){
                $(".left").attr('id', "left-0");
            } else{
                $(".left").attr('id', "left-"+prev);
            }
            $(".left").css("display", "block");
            $(".right").css("display", "none");
            $(".right").attr('id', "right-"+next);
        }
    }

    $(document).ready(function(){
        const starTotal = 5;
        const reviewRating = $('#avg-review').val();
        const ratings = JSON.parse(reviewRating);
        const totalAvgReviews = $("#total-avg-review").val();

        for (const rating in ratings) {
            const starPercentage = (ratings[rating] / totalAvgReviews) * 100;
            const starPercentageRounded = `${(Math.round(starPercentage / 10) * 10)}%`;
            document.querySelector(`.${rating} .stars-inner`).style.width = starPercentageRounded;

        }
        const avgRating = $("#avg-rating").val();
        const starPercentage = (avgRating / starTotal) * 100;
        const starPercentageRounded = `${(Math.round(starPercentage / 10) * 10)}%`;
        $(".totalrating .stars-inner").css("width",starPercentageRounded);
        
        //Voting functionality
        $(".voteugc").click(function() {
            var count   =   0;
            url.setBaseUrl(BASE_URL);
            var customurl = url.build('preview/index/voteugc');
            var ugcId       =    $(this).data("ugc_id");
            var voteType    =    $(this).data("vote");                         

            $.ajax({
                showLoader: true,
                url: customurl,
                type: "POST",
                dataType: "json",
                data: {
                    ugcId: $(this).data("ugc_id"),
                    voteType: $(this).data("vote"),
                },
                complete: function(response) {
                    if(voteType == 'helpful'){
                        count      =    parseInt($("#up-"+ugcId+" .up-votes").text())+1;
                        $("#up-"+ugcId+" .up-votes").text(count);
                    }
                    if(voteType == 'unhelpful'){
                        count       =    parseInt($("#down-"+ugcId+" .down-votes").text())+1;
                        $("#down-"+ugcId+" .down-votes").text(count)
                    }
                    if (response.status == 200) {
                        $("#up-"+ugcId).addClass("active");

                    }
                },
                error: function(xhr, status, errorThrown) {
                    console.log("Error happens. Try again.");
                },
            });
        });
    });
   
});