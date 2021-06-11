/**
 *  Amasty submenu elements UI Component
 *
 *  @desc Component Mega Menu Module
 */

define([
    'jquery',
    'ko',
    'uiComponent',
    'underscore',
    'Amasty_Base/vendor/slick/slick.min'
], function ($, ko, Component, _) {
    'use strict';

    return Component.extend({
        defaults: {
            formKey: $.mage.cookies.get('form_key'),
            content: null,
            hoverTimeout: 350,
            drawTimeOut: null,
            isRoot: true,
            isActive: ko.observable(true),
            isContentActive: ko.observable(true),
            submenuData: [],
            activeElem: {},
            contentBlockTmpl: 'Amasty_MegaMenu/components/submenu/content_block',
            itemsListTmpl: 'Amasty_MegaMenu/components/submenu/items_list',
            itemIconTmpl: 'Amasty_MegaMenu/components/submenu/item_icon',
            itemWrapperTmpl: 'Amasty_MegaMenu/components/submenu/item_wrapper',
            template: 'Amasty_MegaMenu/components/submenu/root_wrapper'
        },
        selectors: {
            formKeyInput: 'input[name="form_key"]',
            mainItem: '.ammenu-item.-main',
            slickSlider: '[data-ammenu-js*="product-slider"], [data-appearance="carousel"]',
            wrapper: '#ammenu-submenu-'
        },

        /**
         * Submenu init method
         */
        initialize: function () {
            var self = this;

            self._super();

            self.node = $(self.selectors.wrapper + self.submenuId);

            self.elems(self.submenuData.elems);
            self.initItems(self.elems(), self);
            this._prepareProductSlider(self);

            self.activeElem = self;
        },

        /**
         * Items Init
         *
         * @param {object} items
         * @param {object} parent
         */
        initItems: function (items, parent) {
            var self = this;

            self.content = self.submenuData.content;
            self.type = self.submenuData.type;
            self.isChildHasIcons = self.submenuData.isChildHasIcons;

            items.forEach(function (item) {
                if (item.elems && item.elems.length) {
                    self.initItems(item.elems, item);
                }

                self.initItem(item, parent);
            });
        },

        /**
         * Item Init
         *
         * @param {object} item
         * @param {object} parent
         */
        initItem: function (item, parent) {
            item.parent = parent;
            item.isActive = ko.observable(false);
            item.isContentActive = ko.observable(false);

            this._prepareProductSlider(item);
        },

        /**
         * Set current item to active state with delay
         *
         * @param {object} item
         */
        setActiveItem: function (item) {
            var self = this;

            if (item === self.activeElem) {
                return;
            }

            self.clearHoverTimeout();

            self.drawTimeOut = setTimeout(function () {
                item.isActive(true);
                item.isContentActive(true);

                self.setParentsTreeState(self.activeElem, false, false);
                self.setParentsTreeState(item, true, true);

                self.activeElem = item;

            }, self.hoverTimeout);
        },

        /**
         * Set Active State for each items up the tree
         *
         * @param {object} item
         * @param {boolean} itemState
         * @param {boolean} contentState
         */
        setParentsTreeState: function (item, itemState, contentState) {
            item.isContentActive(contentState);
            item.isActive(itemState);

            if (item.parent) {
                this.setParentsTreeState(item.parent, itemState, false);
            }
        },

        /**
         * Clearing hover effect interval
         */
        clearHoverTimeout: function () {
            if (this.drawTimeOut !== null) {
                clearInterval(this.drawTimeOut);

                this.drawTimeOut = null;
            }
        },

        /**
         * Prepare product slider
         *
         * @param {object} item
         * @desc preparing product slider if it exist in item content
         */
        _prepareProductSlider: function (item) {
            var self = this,
                mainItem;

            if ($(item.content).find(self.selectors.slickSlider).length) {
                mainItem = $(self.node).parents(self.selectors.mainItem).unbind();

                mainItem.on('mouseenter click', function () {
                    self._setSlidersPosition();
                });

                item.isContentActive.subscribe(function (value) {
                    if (value) {
                        self._setSlidersPosition();
                    }
                });
            }
        },

        /**
         * Update Content
         *
         * @desc Updatind inner content HTML after inserting
         */
        updateContent: _.debounce(function () {
            var self = this,
                formKeyInput = $(self.selectors.formKeyInput);

            if (formKeyInput.val() !== self.formKey) {
                formKeyInput.val(self.formKey);
            }

            $('body').trigger('contentUpdated');

        }, 700),

        /**
         * Slick Slider Position checking
         *
         * @desc checking new slick sliders positions in current submenu category
         */
        _setSlidersPosition: _.debounce(function () {
            var self = this,
                slider = $(self.node).find('.slick-slider');

            slider.slick('slickGoTo', 0);
            slider.slick('setPosition');
            slider.slick('setDimensions');
        }, 700),
    });
});
