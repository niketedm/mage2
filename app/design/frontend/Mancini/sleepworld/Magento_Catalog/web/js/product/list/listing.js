/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

define([
    'ko',
    'underscore',
    'Magento_Ui/js/grid/listing',
    'jquery',
    'owlCarousel'
], function (ko, _, Listing, $) {
    'use strict';

    return Listing.extend({
        defaults: {
            additionalClasses: '',
            filteredRows: {},
            filteredMobRows: {},
            limit: 5,
            listens: {
                elems: 'filterRowsFromCache',
                '${ $.provider }:data.items': 'filterRowsFromServer'
            }
        },

        /** @inheritdoc */
        initialize: function () {
            this._super();
            this.filteredRows = ko.observable();
            this.filteredMobRows = ko.observable(); 
            this.initProductsLimit();
            this.hideLoader();
        },

        /**
         * Initialize product limit
         * Product limit can be configured through Ui component.
         * Product limit are present in widget form
         *
         * @returns {exports}
         */
        initProductsLimit: function () {
            if (this.source['page_size']) {
                this.limit = this.source['page_size'];
            }

            return this;
        },

        /**
         * Initializes observable properties.
         *
         * @returns {Listing} Chainable.
         */
        initObservable: function () {
            this._super()
                .track({
                    rows: []
                });

            return this;
        },

        /**
         * Sort and filter rows, that are already in magento storage cache
         *
         * @return void
         */
        filterRowsFromCache: function () {
            this._filterRows(this.rows);
        },

        /**
         * Sort and filter rows, that are come from backend
         *
         * @param {Object} rows
         */
        filterRowsFromServer: function (rows) {
            this._filterRows(rows);
        },

        /**
         * Filter rows by limit and sort them
         *
         * @param {Array} rows
         * @private
         */
        _filterRows: function (rows) {
            var rowDetails = _.sortBy(rows, 'added_at').reverse().slice(0, this.limit);
            this.filteredRows(rowDetails);
            var mobileRows = rowDetails.reduce((all,one,i) => {
                const ch = Math.floor(i/2); 
                all[ch] = [].concat((all[ch]||[]),one); 
                return all
            }, [])
            this.filteredMobRows(mobileRows);
        },

        getQty: function (dd){
            return dd.extension_attributes.qty;
        },

        /**
         * Can retrieve product url
         *
         * @param {Object} row
         * @returns {String}
         */
        getUrl: function (row) {
            return row.url;
        },

        /**
         * Get product attribute by code.
         *
         * @param {String} code
         * @return {Object}
         */
        getComponentByCode: function (code) {
            var elems = this.elems() ? this.elems() : ko.getObservable(this, 'elems'),
                component;

            component = _.filter(elems, function (elem) {
                return elem.index === code;
            }, this).pop();

            return component;
        },

        pdpRvowlInit: function(){
            $('.product-items').owlCarousel({
                nav: false,
                margin: 20,
                responsive: {
                    0: {
                        items: 1,
                        loop: false,
                    },
                    426: {
                        items: 3,
                        loop: false,
                        margin: 30,
                    },
                    768: {
                        items: 5,
                        loop: false,
                        margin: 30,
                    }
                }
            });
            $('.mobile-items').owlCarousel({
                nav: false,
                margin: 20,
                responsive: {
                    0: {
                        items: 1,
                        loop: false,
                    },
                    426: {
                        items: 3,
                        loop: false,
                        margin: 30,
                    },
                    768: {
                        items: 5,
                        loop: false,
                        margin: 30,
                    }
                }
            });
        }
    });
});
