require([
    "jquery"
], function ($, url, priceUtils) {
    $(document).ready(function () {
        $('#mobile').attr('placeholder','999-999-9999');
        $('#mobile').on("keypress",function(e){
            var strlength = parseInt(this.value.length);
            if(strlength > 11){
                e.preventDefault();
            }
        });
        $('#mobile').keyup(function(e){
            var numbers = this.value.replace(/\D/g, ''),
            char = {0:'',3:'-',6:'-'};
            this.value = '';
            for (var i = 0; i < numbers.length; i++) {
                this.value += (char[i]||'') + numbers[i];
            }
        }); 
    });
});