require([
    "jquery",
    "mage/url",
    'Magento_Catalog/js/price-utils',
    'select2'
], function ($, url, priceUtils) {
    $(document).ready(function () {
        $('.loading-mask').hide();
        $("#foundationprd").select2({
            minimumResultsForSearch: 10,
            templateResult: formatState,
            templateSelection: formatFndDropdown
        });
        var sizeid = $("#sizeid").val();
        var colorid= $("#colorid").val();
        $('#'+sizeid).change(function () {
            if($('#'+colorid).length){
                $('#sizeprice').text("$0.00");
            } else {
                setTimeout(function () {
                   loadFoundation();
                }, 500);
            }
        });

        $("#"+colorid).change(function(){
            setTimeout(function () {
                loadFoundation();
            }, 500);
        });
    });

    function loadFoundation(){
        simpleProductId = $("input[name=selected_configurable_option]").val();
                url.setBaseUrl(BASE_URL);
                var link = url.build('foundation/foundationajax');

                $.ajax({
                    showLoader: true,
                    url: link,
                    data: { selectprd: simpleProductId },
                    type: "POST",
                    dataType: 'json'
                }).done(function (data) {
                    if (data.foundation == 0) {
                        $('#foundation').css("display", "none");
                        $('#sizeprice').text(data.sizeprice);
                        $("#fndprice").text("$0.00");
                        price = "$0.00";
                        calculatePrice(price);
                    } else {
                        $('#foundation').css("display", "block");
                        $('#foundationprd').prop('disabled', false);
                        $('#foundationprd').empty();
                        $('#foundationprd').append(data.value);
                        $('#sizeprice').text(data.sizeprice);
                        $('.price-box').text(data.sizeprice);
                        $("#fndprice").text("$0.00");
                        $("#foundationprd").select2({
                            minimumResultsForSearch: 10,
                            templateResult: formatState,
                            templateSelection: formatFndDropdown
                        });
                    }

                });
    }
    function formatState(opt) {

        var optimage = $(opt.element).attr('data-image');
        var price = $(opt.element).attr('data-price');

        if (!opt.id) {
            /*   $("#fndprice").text("$0.00"); 
              price="$0.00";
              calculatePrice(price);
              return opt.text; */
        }
        if (!optimage) {
            return opt.text;
        } else {

            var $opt = $(
                '<span><img style="height:25px; width:25px" src="' + optimage + '"/> ' + opt.text + '<span style="float:right;">' + price + '</span></span>'
            );

            if (opt.selected) {

                //$("#fndprice").text(price); 
                //calculatePrice(price);
                //$('#temp-total').text(tempTotal);              
            }

            return $opt;
        }
    };
    function formatFndDropdown(opt) {
        if (!opt.id) {
            $("#fndprice").text("$0.00");
            price = "$0.00";
            calculatePrice(price);
            return opt.text;
        }
        var optimage = $(opt.element).attr('data-image');
        var price = $(opt.element).attr('data-price');

        if (!optimage) {
            return opt.text;
        } else {
            var $opt = $(
                '<span>' + opt.text + '</span>'
            );

            if (opt.selected) {
                $("#fndprice").text(price);
                calculatePrice(price);
            }
            return $opt;
        }
    }
    function calculatePrice(price) {
        var sizeTotal = $('#sizeprice').text();
        var symbol = sizeTotal.slice(0, 1);
        var planPrice = $("#planprice").text();
        var foundPrice = price;

        var sizePrice = sizeTotal.replace(symbol, " ");
        var foundPrice = foundPrice.replace(symbol, " ");
        var planPrice = planPrice.replace(symbol, " ");
        sizePrice = sizePrice.replace(",", "");
        foundPrice = foundPrice.replace(",", "");
        planPrice = planPrice.replace(",", "");

        var subtotal = parseFloat(sizePrice) + parseFloat(foundPrice) + parseFloat(planPrice);
        tempTotal = priceUtils.formatPrice(subtotal);
        $('.price-box').text(tempTotal);
    }
});