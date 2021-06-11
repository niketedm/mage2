require([
    "jquery",
    "mage/url",
    'Magento_Catalog/js/price-utils',
    'catalogAddToCart',
    'select2'
], function ($, url) {
    $(document).ready(function () {
        $('#attribute188').change(function () {
            if($('#attribute93').length){
            } else {
                setTimeout(function () {
                   loadCompare();
                }, 500);
            }
        });

        $("#attribute93").change(function(){
            setTimeout(function () {
                loadCompare();
            }, 500);
        });
    });

    function loadCompare(){
        simpleProductId     =   $("input[name=selected_configurable_option]").val();
        console.log("simple compare"+ simpleProductId);

        url.setBaseUrl(BASE_URL);
        var link = url.build('compare/index');

        $.ajax({
            context:'#compareresponse',
            showLoader: true,
            url: link,
            data: {currentproduct : simpleProductId},
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
           $('#compareresponse').html(data.output);
           $("form[data-role='tocart-form']" ).catalogAddToCart();
           return true;
        });
       
    }
    

});