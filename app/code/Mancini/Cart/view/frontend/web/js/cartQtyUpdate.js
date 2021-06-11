define([
    'jquery',
    'Magento_Checkout/js/action/get-totals',
    'Magento_Customer/js/customer-data',
    "mage/url",
    'select2'
], function ($, getTotalsAction, customerData, url) {
 
    url.setBaseUrl(BASE_URL);
    var urlprotect      =   url.build('updatecart/index/delete');
    var urlwishlist     =   url.build('customwishlist/index/wishlist');
    var addToCartUrl    =   url.build('updatecart/protector');

    $(document).ready(function(){
        $(".protection").select2({
            minimumResultsForSearch: 5,
            templateResult: formatprotect,
            templateSelection: formatprotect
        }); 
        $(document).on('change', 'input[name$="[qty]"]', function(){
            if($(this).hasClass('protector')){
                var className   =   $(this).closest("div").attr('class');
                var classSplit  =   className.split(" "); 
                var protectplanId   =   classSplit[1];
                var qty     =  $(this).val(); 
                $("#"+protectplanId+" .qty").val(qty);
            }
            var form = $('form#form-validate');
            $.ajax({
                url: form.attr('action'),
                data: form.serialize(),
                showLoader: true,
                success: function (res) {
                    var parsedResponse = $.parseHTML(res);
                    var result = $(parsedResponse).find("#form-validate");
                    var sections = ['cart'];
 
                    $("#form-validate").replaceWith(result);
 
                    /* Minicart reloading */
                    customerData.reload(sections, true);
 
                    /* Totals summary reloading */
                    var deferred = $.Deferred();
                    getTotalsAction([], deferred);
                },
                error: function (xhr, status, error) {
                    var err = eval("(" + xhr.responseText + ")");
                    console.log(err.Message);
                }
            });
        });
        $('.qty-button').unbind('click');
        $(".qty-button").on("click",function(){
            var btnId = $(this).attr("id");
            var clickDetails = btnId.split("-");
            var productId = clickDetails[1];

            //Quantity Increment
            if(clickDetails[0] == "plus"){
                var currentQty = parseInt($("#cart-"+productId+"-qty").val());
                var newQty = currentQty + 1;
                $("#cart-"+productId+"-qty").val(newQty);
                $("#cart-"+productId+"qtyplusminus").val("plus");
            }

            //Quantity Decrement 
            if(clickDetails[0] == "minus"){
                var currentQty = parseInt($("#cart-"+productId+"-qty").val());
                var newQty = currentQty - 1;
                if( newQty < 1){
                    newQty =  1;
                }
                $("#cart-"+productId+"-qty").val(newQty);
                $("#cart-"+productId+"qtyplusminus").val("minus");
            }
            location.reload();
            $(".qty").trigger('change');
        });
        //Add Selected protector to the cart
        $(".addprotect").on("click",function(){
            var selectedPrd =   $(this).attr("id");
            var nameQuote   =   $(this).attr('name');
            var quotedetails=   nameQuote.split("-");
            var selections  =   selectedPrd.split("-");
            var mainPrd     =   selections[1];
            var quoteId     =   quotedetails[1];
            var qtyPlan     =   $(".qty").val();

            var proPlanId = $("#protection-"+mainPrd+" option:selected" ).val();
            var data = {productid: proPlanId, quoteid:quoteId, qty:qtyPlan};

            $.ajax({
                url : addToCartUrl,
                dataType : 'json',
                type : 'POST',
                data: data,
                showLoader: true,
                success : function(res)
                {
                    location.reload();
                },
                error : function()
                {
                    location.reload();
                }
            });

        });

        //for removing protection plan products 'remove protector' link
        $(".removeprotect").click(function() {
            var prdQuoteId              =   $(this).attr("name");
            var prdProtId               =   $(this).attr("id");
            var quotedetails            =   prdQuoteId.split("-");
            var protectionplandetails   =   prdProtId.split("-");
            var quoteId                 =   quotedetails[1];

            var mainprditemid           =   quotedetails[2];
            var mainPrd                 =   protectionplandetails[1];

            var url                     =   urlprotect;

            $.ajax({
                url: url,
                type: "POST",
                data: {
                    protectionproduct: mainPrd, 
                    quoteitemid      : quoteId,
                    mainprditemid    : mainprditemid
                },
                showLoader: true,
                cache: false,
                success: function(response) {
                    location.reload();
                }
            });
        });

        //wishlist code here
        $(".custom-wishlist").click(function() {
            var productId = $(this).attr('data-productid');
            var link      = urlwishlist;
            $.ajax({
                showLoader: true,
                url: link,
                data: {
                    productId: productId,
                    method: 'singleproduct'
                },
                type: "POST",
                dataType: 'json'
            }).done(function(data) {
                console.log(data.productid);
                if (data.productid == 'false') {
                    var redirectlink = url.build('customer/account/login');
                    window.location.replace(redirectlink);
                } else {
                    $.each(data.productid, function(index, value) {
                        var productId = value;
                        var sections = ['wishlist'];
                        customerData.invalidate(sections);
                        customerData.reload(sections, true);
                    });
                }

            });
        });    
    });

    function formatprotect (opt) {
        if (!opt.id) {
            return opt.text; 
        } 
                         
        var $opt = $(
            '<span>' + opt.text + '</span>'
        );
        return $opt;
    };
});