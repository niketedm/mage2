require([
    "jquery",
    "mage/url",
    'Magento_Catalog/js/price-utils',
    'select2'
], function ($, url) {
    $(document).ready(function () {
        $('#attribute188').change(function () {
            if($('#attribute93').length){
            } else {
                setTimeout(function () {
                   loadSimilar();
                }, 500);
            }
        });

        $("#attribute93").change(function(){
            setTimeout(function () {
                loadSimilar();
            }, 500);
        });
    });

    function loadSimilar(){
        simpleProductId = $("input[name=selected_configurable_option]").val();

        url.setBaseUrl(BASE_URL);
        var link = url.build('similar/index');

        $.ajax({
            context: '#similarresponse',
            showLoader: true,
            url: link,
            data: { currentproduct: simpleProductId },
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            $('#similarresponse').html('');
            $('#similarresponse').html(data.output);
            $(".owlSP").owlCarousel({
                nav: false,
                margin: 30,
                responsive: {
                    0: {
                        items: 1,
                        loop: false,
                    },
                    426: {
                        items: 3,
                        loop: false,
                    },
                    769: {
                        items: 4,
                        loop: false,
                    }
                }
            });
            return true;
        });
       
    }
    

});