define([
    'jquery',
    'underscore',
    'mage/translate',
    'jquery-ui-modules/widget',
    'select2'
], function ($ , _ , $t) {
    'use strict';

    /** Ths is the Mixin for Mage Configurable Widget */
    var configurableWidgetMixin = {
         /**
         * Creates widget
         * @private
         */
        _create: function () {
            this._super();
            this._initializeSelect2();
        },

        /**
         * Here we are initializing the select2 jQuery plugin for each configurable select attribute
         * Select2 is used for rendering the images for each options
         */
        _initializeSelect2: function(){
            $.each(this.options.settings, $.proxy(function (index, element) {
                var $element = $(element);
                var id = $element.attr('id');
                var attrImages = this. getAttributeImagesById(id);
                var attrDimension = this.getAttributeDimensions(id);
                var attrFinalPrice = this.getAttributeFinalPrice(id);
                var isSelection = true;

                // To make the first option selected by default
                $element.find('option:eq(0)').remove();  
                $element.find('option:eq(0)').prop('selected','selected').trigger('change');                
                $element.select2({
                    templateResult: this.getOptionTemplate(attrImages, attrDimension,attrFinalPrice),
                    templateSelection:this.getOptionTemplate(attrImages, attrDimension, attrFinalPrice,isSelection),
                    minimumResultsForSearch: 10,
                    
                });

            }, this));
        },
        /**
         * Here we are mapping option label to the option image
         * @param {*} id 
         */
        getAttributeDimensions: function(id){
            var attrId = id.replace('attribute','');
            var attrOptions = this.options.spConfig.attributes[attrId].options;

            return _.reduce(attrOptions, function( dimension ,opt){ 
                dimension[opt.label.toLowerCase()] = opt.dimension;
                return dimension;
            }, {});

        },
        /**
         * Here we are mapping option label to the option dimension
         * @param {*} id 
         */
        getAttributeImagesById: function(id){
            var attrId = id.replace('attribute','');
            var attrOptions = this.options.spConfig.attributes[attrId].options;

            return _.reduce(attrOptions, function( images ,opt){ 
                images[opt.label.toLowerCase()] = opt.url;
                return images;
            }, {});

        },
          /**
         * Here we are mapping option label to the final price
         * @param {*} id 
         */
        getAttributeFinalPrice: function(id){
            var attrId = id.replace('attribute','');
            var attrOptions = this.options.spConfig.attributes[attrId].options;

            return _.reduce(attrOptions, function( finalPrice ,opt){ 
                finalPrice[opt.label.toLowerCase()] = opt.finalprice;
                return finalPrice;
            }, {});

        },
        /**
         * Function that render option with images
         * @param {*} attrImages 
         * @param {*} isSelection 
         */
        getOptionTemplate: function(attrImages, attrDimension, attrFinalPrice, isSelection = false){
            var templateResult =  function ( state) {
                var stateImage = attrImages[state.text.toLowerCase()];
                var dimension  = attrDimension[state.text.toLowerCase()];
                var stateText  = state.text;
                var prdPrice   = attrFinalPrice[state.text.toLowerCase()];

                //If the size attribute contains price
                if (stateText.indexOf('+') > -1) {
                    var res = stateText.split("+");
                    stateText = res[0];
                }

                if (!state.id || !stateImage ) {
                    if (dimension == 0 || typeof dimension == "undefined"){
                        return $t(stateText); 
                    } else {
                        var $state = $(
                            '<span style="display: flex;"><span style="line-height: 25px;">' + $t(stateText) + '</span> <span style="padding-left:61px;line-height: 18px;">' + dimension + '</span></span>'
                        );
                    }
                }
                if(typeof prdPrice == "undefined") { 
                    var prdtext = '';
                }else{
                    var prdtext = '<span style="padding-left:40px;line-height: 25px;float:right;">' + $t(prdPrice) + '</span>';
                }
                if(dimension == 0 || typeof dimension == "undefined"){
                    var $state = $(
                        '<span ><img style="height:25px; width:25px" src="'+stateImage+ '" /> <span style="padding-left:40px;line-height: 25px;">' + $t(stateText) + '</span>' + prdtext + '</span>'
                    );
                } else {
                    var $state = $(
                        '<span><img style="height:25px; width:25px" src="'+stateImage+ '" /> <span style="padding-left:40px;line-height: 25px;">' + $t(stateText) + '</span><span style="padding-left:5px;line-height: 25px;">' + dimension + '</span> ' + prdtext + ' </span>'
                    );
                }
               
                /*== Fucntion for dropdown selection ===*/
                if (isSelection) {
                    if(dimension == 0 || typeof dimension == "undefined"){
                        var $state = $(
                            '<span style="display: flex; margin-top: 5px;"><span style="line-height: 15px;font-size: 16px;">' + $t(stateText) + '</span> </span>'
                        );
                    } else{
                        var $state = $(
                            '<span style="display: flex; margin-top: 5px;"><span style="line-height: 15px;">' + $t(stateText) + '</span> <span style="padding-left:61px;line-height: 18px;font-size: 16px;">' + dimension + '</span></span>'
                        );
                    }
                   
                }

                return $state;
            };

            return templateResult;
        },

    };

    return function (targetWidget) {
        // Example how to extend a widget by mixin object
        $.widget('mage.configurable', targetWidget, configurableWidgetMixin); // the widget alias should be like for the target widget

        return $.mage.configurable; //  the widget by parent alias should be returned
    };
});