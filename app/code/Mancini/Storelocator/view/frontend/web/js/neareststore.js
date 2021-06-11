define([
    "jquery",
    "mage/url",
    "jquery/ui",
    "mage/cookies",
], function ($, url) {
    'use strict';
    $.widget('mage.storeloc', {
        latitude: 0,
        longitude: 0,
        options: {
            emptyMsg: ('Enter Zipcode')
        },
        _create: function () {
            $('#update').prop('disabled', true);

            this.initializeChange();
        },
        successmap: function success(pos) {
            var crd = pos.coords;

            this.latitude = crd.latitude;
            this.longitude = crd.longitude;
            url.setBaseUrl(BASE_URL);
            var ajaxUrl = url.build('storeloc/index/nearest');

            //AJAX call to get the nearest stores
            $.ajax({
                context: '#neareststore',
                showLoader: true,
                url: ajaxUrl,
                data: { latitude: this.latitude, longitude: this.longitude },
                type: "POST",
                dataType: 'json'
            }).done(function (data) {
                if (data.output.nearestset == 1) {
                    var storelistVal = data.output.address + "," + data.output.state;
                    $(".amlocator-store-list .zipcode").html(storelistVal);
                    $("#currentloc").html(data.output.state + " " + data.output.zip);
                } else {
                    $(".amlocator-store-list .zipcode").html("NA");
                    $("#currentloc").html(data.output.custstate + " " + data.output.custzip);
                }
                $("#storelistresponse").html("");
                $("#storelist-outer").attr("style", "display:none");
                $("#changeloc").attr("style", "display:block");

                return true;
            });

        },

        errormap: function error(err) {

        },

        initializeChange: function () {
            var options = {
                enableHighAccuracy: true,
                timeout: 5000,
                maximumAge: 0
            };

            if (!$.mage.cookies.get('custzipcode')) {
                //run function here
                navigator.geolocation.getCurrentPosition(this.successmap, this.errormap, options);
            } else {
                $(".amlocator-store-list .zipcode").html($.mage.cookies.get('custloc'));
                $("#currentloc").html($.mage.cookies.get('custzipcode'));
            }

            $("#change").click(function () {
                $("#changeloc").attr("style", "display:none");
                $("#changediv").attr("style", "display:block");
            });

            $('input[type="text"]').keyup(function () {
                if ($(this).val() != '') {
                    $('#update').prop('disabled', false);
                }
            });

            $("#changediv").find('.btn-update').click(function () {
                var zipCode = $("#newzip").val();
                url.setBaseUrl(BASE_URL);
                var ajaxUrl = url.build('storeloc/changezip');

                //AJAX call to get the nearest stores
                $.ajax({
                    context: '#storelistresponse',
                    showLoader: true,
                    url: ajaxUrl,
                    data: { zipcode: zipCode },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    $("#storelist-outer").attr("style", "display:block");
                    $('#storelistresponse').html(data.output);
                    $("#changediv").attr("style", "display:none");
                    $("#changeloc").attr("style", "display:block");
                    $("#newzip").val('');
                    return true;
                });

            });

            $("#storelistresponse").on("click", ".location-name", function () {
                var storeid = $(this).attr('id');
                var distance = $("#dist-" + storeid).text();
                url.setBaseUrl(BASE_URL);
                var ajaxUrl = url.build('storeloc/updatezip');

                var currentClass =
                    //AJAX call to get the nearest stores
                    $.ajax({
                        context: '#storelistresponse',
                        showLoader: true,
                        url: ajaxUrl,
                        data: { storeid: storeid, distance: distance },
                        type: "POST",
                        dataType: 'json'
                    }).done(function (data) {
                        var storelistVal = data.output.address + "," + data.output.city + "," + data.output.state;
                        var directionUrl = url.build('amlocator/' + data.output.url_key);
                        var distance = parseFloat(data.output.distance);
                        var roundedDist = distance.toFixed(1);

                        //Show the updated Nearest store in common block
                        var tryBeforeContent = '<div class="SL-name">' + data.output.name + '</div><div class="addr-dir"><div class="addr-name"><div class="SL-address">' + data.output.address + ', <br>' + data.output.city + ', ' + data.output.state + ', ' + data.output.zip + '<br>Call: <a tabindex="0" href="tel:+1 ' + data.output.phone + '">+1 ' + data.output.phone + '</a>' + '</div></div><div class="dist-dir"><div class="SL-distance">' + roundedDist + ' Miles</div><div class="direc"><a href=' + directionUrl + '> Directions </a></div></div></div>';

                        //Show updated store in home
                        var homecontent = data.output.address + ', ' + data.output.state + ' ' + data.output.zip;
                        var phoneHome = '<a tabindex="0" href="tel:+1 ' + data.output.phone + '"> ' + data.output.phone + '</a>';
                        $("#homepage-store .contact-location").html(homecontent);
                        $("#homepage-store .store-phone").html(phoneHome);
                        $(".nearest-loc-footer").attr("style", "display:block");
                        $(".na-store-footer").attr("style", "display:none");

                        $(".SL-tbb").removeClass("no-loc");
                        $(".SL-tbb").html(tryBeforeContent);
                        $(".amlocator-store-list .zipcode").html(storelistVal);
                        $("#storelistresponse").html("");
                        $("#storelist-outer").attr("style", "display:none");
                        $(".store-locator-header .block-minicart").attr("style", "display:none");
                        $("#changeloc").attr("style", "display:block");
                        if ($("body").hasClass("page-products") || $("body").hasClass("cms-contact-us") || $("body").hasClass("cms-page-view")) {
                            location.reload();
                        }
                        return true;
                    });
            });
        }
    });
    return $.mage.storeloc;
});