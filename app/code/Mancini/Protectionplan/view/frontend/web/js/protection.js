require([
    "jquery",
    "mage/url",
    'Magento_Catalog/js/price-utils',
    'select2'
], function ($, url, priceUtils) {
    
    $(document).ready(function () {
        $("#protectionprd").select2({
            minimumResultsForSearch: 10,
            templateResult: formatprotect,
            templateSelection: formatDropdown
        });        
        var sizeid = $("#sizeid").val();
        var colorid= $("#colorid").val();  
        $('#'+sizeid).change(function () {
            if($('#'+colorid).length){
                $('#sizeprice').text("$0.00");
            } else {
                setTimeout(function () {
                   loadProtection();
                }, 500);
            }
        });
        $('#'+colorid).change(function(){
            setTimeout(function () {
                loadProtection();
            }, 500);
        });
    });

    function loadProtection(){
        simpleProductId     =   $("input[name=selected_configurable_option]").val();
        url.setBaseUrl(BASE_URL);
        var link = url.build('protection/index');
        $.ajax({
            showLoader: true,
            url: link,
            data: {selectprd : simpleProductId},
            type: "POST",
            dataType: 'json'
        }).done(function (data) {
            if(data.protection == 0) {
                $('#protection').css("display","none");
                $("#planprice").text("$0.00");
                price="$0.00";
                calculatePlanPrice(price);
            } else {
                $('#protection').css("display","block");
                $("#selected-protection-prd").val('');
                $('#protectionprd').prop('disabled', false);
                $('#protectionprd').empty();
                $("#planprice").text("$0.00");
                $('#protectionprd').append(data.value);
                $("#protectionprd").select2({
                    minimumResultsForSearch: 10,
                    templateResult: formatprotect,
                    templateSelection: formatDropdown
                });
            }
        });
    }

    function formatprotect (opt) {
      
        var optimage = $(opt.element).attr('data-image'); 
        var price    = $(opt.element).attr('data-price');

        if (!opt.id) {
         /*    $("#planprice").text("$0.00"); 
            price="$0.00";
            calculatePlanPrice(price);
            return opt.text; */
        } 
        
        if(!optimage){
           return opt.text;
        } else {                    
            var $opt = $(
                '<span><img style="height:25px; width:25px" src="' + optimage + '"/> ' + opt.text + '<span style="float:right;">'+ price +'</span></span>'
             );
            return $opt;
        }
    };
    function formatDropdown(opt){
        if (!opt.id) {
            $("#planprice").text("$0.00");
            price="$0.00";
            calculatePlanPrice(price);
            return opt.text;
        }  
        var optimage = $(opt.element).attr('data-image'); 
        var price    = $(opt.element).attr('data-price');
        
        if(!optimage){
           return opt.text;
        } else {                    
            var $opt = $(
                '<span>' + opt.text + '</span>'
             );

            if (opt.selected) {
                $("#planprice").text(price);    
                calculatePlanPrice(price);
            }
            return $opt;
        }
    };
    function calculatePlanPrice(price){
        var sizeTotal   =   $('#sizeprice').text();
        var symbol      =   sizeTotal.slice(0, 1);
        var planPrice   =   price;
        var foundPrice  =   $("#fndprice").text();

        var sizePrice   =   sizeTotal.replace(symbol," ");
        var foundPrice  =   foundPrice.replace(symbol," ");
        planPrice       =   planPrice.replace(symbol," ");
        sizePrice       =   sizePrice.replace(",","");
        foundPrice      =   foundPrice.replace(",","");
        planPrice       =   planPrice.replace(",","");

        var subtotal    =   parseFloat(sizePrice) + parseFloat(foundPrice) +  parseFloat(planPrice);
        tempTotal = priceUtils.formatPrice(subtotal);
        $('.price-box').text(tempTotal);
    };
});
