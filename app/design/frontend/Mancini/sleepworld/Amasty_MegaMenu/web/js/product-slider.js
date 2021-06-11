define([
    'jquery',
    'Amasty_Base/vendor/slick/slick.min'
], function ($) {
    $.widget('amasty.productSlider', {
        selectors: {
            menuToggle: '[data-ammenu-js="collapse-trigger"]'
        },
        options: {},

        _create: function () {
            var self = this,
                parent = $(this.element).parents('.-main');

            $(this.element).slick(this.options);

            parent.on('mouseenter', function () {
                setTimeout(function () {
                    $(self.element).slick('setPosition');
                }, 400);
            });

            parent.find(this.selectors.menuToggle).on('click', function () {
                $(self.element).slick('setPosition');
            });
        }
    });

    return $.amasty.productSlider;
});
