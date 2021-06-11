/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

var config = {
    map: {
        '*': {
            select2:     'Mancini_Productdetail/js/select2.min',
            foundation:  'Mancini_Productdetail/js/foundationselect'
        }
    },
    config:{
        mixins: {
            'Magento_ConfigurableProduct/js/configurable': {
                'Mancini_Productdetail/js/configurable-mixin': true
            },
        }
    }
};
