var config = {
	config: {
        mixins: {
            'Magento_Checkout/js/sidebar': {
                'Mancini_MinicartQty/js/sidebar': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/template/minicart/item/default.html': 'Mancini_MinicartQty/template/minicart/item/default.html'
        }
    }
};