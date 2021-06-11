define(['Magento_Checkout/js/view/summary/abstract-total', 'Magento_Checkout/js/model/quote','Magento_Catalog/js/price-utils','Magento_Checkout/js/model/totals'], function (Component, quote, priceUtils, totals) 
{
        "use strict";
        
        return Component.extend(
        {
            defaults: 
            {
                isFullTaxSummaryDisplayed: window.checkoutConfig.isFullTaxSummaryDisplayed || false,
                template: 'Anowave_Fee/checkout/summary/fee_tax'
            },
            totals: quote.getTotals(),
            isTaxDisplayedInGrandTotal: window.checkoutConfig.includeTaxInGrandTotal || false,
            isDisplayed: function() 
            {
                return this.isFullMode();
            },
            getValue: function() 
            {
                var price = 0;
                
                if (this.totals()) 
                {
                    price = totals.getSegment('fee_tax').value;
                }
                
                return this.getFormattedPrice(price);
            },
            getTitle: function()
            {
            	if (this.totals())
            	{
            		return totals.getSegment('fee_tax').title;
            	}
            	
            	return 'Fee Tax';
            },
            getStyle: function()
            {
            	if (this.totals())
            	{
            		var fee = parseFloat(totals.getSegment('fee_tax').value);
            		
            		if (!fee)
            		{
            			return 'none';
            		}
            	}
            	
            	return 'table-row';
            },
            getBaseValue: function() 
            {
                var price = 0;
                
                if (this.totals()) 
                {
                    price = this.totals().base_fee;
                }
                
                return priceUtils.formatPrice(price, quote.getBasePriceFormat());
            }
        });
    }
);