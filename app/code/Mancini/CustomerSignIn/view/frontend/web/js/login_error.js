require([
    "jquery"
], function ($) {
        $(document).ready(function() {
            $( "#loginPass" ).keypress(function() {
                if(document.getElementById("loginPass").value.length>0){
                   var temp =  document.getElementById("loginPass-error");
                //    console.log('Temp', temp);
                   temp.style.display = "none";
                }
            });
        })
    });
