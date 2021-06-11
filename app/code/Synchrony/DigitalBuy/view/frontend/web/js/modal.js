var DigitalBuyModalManager = (function () {
    var _config = {
        modalId: "digBuyModal",
        globalLoaderId: "checkout-loader",
        digitalBuyObj: "syfDBuy",
        digitalBuyInitFunctionName: "calldBuyProcess"
    };

    var extend = function () {
        for(var i=1; i<arguments.length; i++)
            for(var key in arguments[i])
                if(arguments[i].hasOwnProperty(key))
                    arguments[0][key] = arguments[i][key];
        return arguments[0];
    };

    var showLoader = function () {
        if (!_config.globalLoaderId) {
            return;
        }

        var el = document.getElementById(_config.globalLoaderId);
        if (!el) {
            return;
        }

        el.style.display = "block";
    };

    var hideLoader = function () {
        if (!_config.globalLoaderId) {
            return;
        }

        var el = document.getElementById(_config.globalLoaderId);
        if (!el) {
            return;
        }

        el.style.display = "none";
    };

    var closeEventCallback = function (event) {
        if (typeof event.data == "string"
            && (event.data == "Close Model" || event.data == "Return To Merchant Shipping" || event.data == "Close")) {
            showLoader();
            document.getElementById(_config.postbackFormId).submit();
        }
    };

    return {
        init: function (config) {
            extend(_config, config);
            var form = document.getElementById(_config.formId);
            var initFunctionScope = _config.digitalBuyObj ? window[_config.digitalBuyObj] : window;

            hideLoader();
            initFunctionScope[_config.digitalBuyInitFunctionName](form);

            if (window.addEventListener) {
                window.addEventListener("message", closeEventCallback);
            } else if (window.attachEvent) {
                window.attachEvent("onmessage", closeEventCallback);
            }
        }
    };
})();
