define([
    'jquery'
], function ($) {

    $.widget('amasty.megaMenu', {
        options: {
            hamburgerStatus: 0,
            desktopStatus: 0,
            stickyStatus: 0,
        },
        classes: {
            opened: '-opened',
            noScroll: '-am-noscroll'
        },

        _create: function () {
            var self = this,
                isMobile = $(window).width() <= 1024 ? 1 : 0,
                isDesktop = self.options.desktopStatus,
                isHamburger = self.options.hamburgerStatus,
                isSticky = self.options.stickyStatus;

            self._addindOsPlatformClass();

            $('[data-ammenu-js="menu-toggle"]').off('click').on('click', function () {
                self.toggleMenu();
            });

            if (!isHamburger) {
                $('[data-ammenu-js="menu-overlay"]').on('swipeleft click', function () {
                    self.toggleMenu();
                });

                $('[data-ammenu-js="tab-content"]').on('swipeleft', function () {
                    self.toggleMenu();
                });

                if (isMobile) {
                    $(window).on('swiperight', function (e) {
                        var target = $(e.target);

                        if (e.swipestart.coords[0] < 50
                            && !target.parents().is('.ammenu-nav-sections')
                            && !target.is('.ammenu-nav-sections')) {
                            self.toggleMenu();
                        }
                    });
                }
            }

            if (isDesktop && isSticky) {
                var menu = $('[data-ammenu-js="desktop-menu"]'),
                    menuOffsetTop = menu.offset().top;

                $(window).on('scroll', function () {
                    menu.toggleClass('-sticky', window.pageYOffset >= menuOffsetTop);
                });
            }

            this.removeEmptyPageBuilderItems();
        },

        _addindOsPlatformClass: function () {
            $('body').addClass('-' + navigator.platform.split(' ')[0].toLowerCase());
            $('body').addClass(navigator.userAgent.indexOf('Trident/') > 0 ? '-ie' : '');
        },

        toggleMenu: function () {
            $('body').toggleClass(this.classes.noScroll);
            $('[data-ammenu-js="menu-toggle"]').toggleClass('-active');
            $('[data-ammenu-js="desktop-menu"]').toggleClass('-hide');
            $('[data-ammenu-js="nav-sections"]').toggleClass(this.classes.opened);
            $('[data-ammenu-js="menu-overlay"]').fadeToggle(50);
        },

        removeEmptyPageBuilderItems: function () {
            $('[data-ammenu-js="menu-submenu"]').each(function () {
                var element = $(this),
                    childsPageBuilder = element.find('[data-element="inner"]');

                if (childsPageBuilder.length) {
                    //remove empty child categories
                    $('[data-content-type="ammega_menu_widget"]').each(function () {
                        if (!$(this).children().length) {
                            $(this).remove();
                        }
                    });

                    var isEmpty = true;
                    $(childsPageBuilder).each(function () {
                        if ($(this).children().length) {
                            isEmpty = false;
                            return true;
                        }
                    });

                    if (isEmpty) {
                        element.remove();
                    }
                }
            });
        }
    });

    return $.amasty.megaMenu;
});
