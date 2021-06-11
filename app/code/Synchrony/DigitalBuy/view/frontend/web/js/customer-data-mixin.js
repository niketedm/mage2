define(['jquery', 'mage/utils/wrapper'], function ($, wrapper) {
    'use strict';

    var mixin = {
        reload: function (origReload, sectionNames, forceNewSectionTimestamp) {
            if (typeof window.skipCustomerDataReload != 'undefined' && window.skipCustomerDataReload == true) {
                return $.Deferred();
            }
            return origReload(sectionNames, forceNewSectionTimestamp);
        }
    };

    return function (target) {
        return wrapper.extend(target, mixin);
    };
});
