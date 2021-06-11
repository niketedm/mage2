3.0.6
=============
* Support BOTH SetPay and Revolving
* Update Field Level Validation Issue causing unwanted Authorization Declines
* Resolve issue with Promo Text and use of Default Promo

3.0.5
=============
* Compatibility to Magento v2.4.2

3.0.4
=============
* Fixed:
    * Exception error in marketing config path.

3.0.3
=============
* Resubmitting to the Marketplace

3.0.2
=============
* Upload to the marketplace

3.0.1
=============
* Addressed:
	* Compatibility with Magento 2.4
	* Requires Vertex extension being disabled from the command line
	* Addresses the rounding where 3 decimal places are rounded to 2
	* Addresses the zip code where zip plus 4 is truncated to zip 5

3.0.0
=============
* Added support for PHP 7.4

2.1.3
=============
* Fixed:
    * Custom CMS variables not available in WYSIWYG editor nor rendered on frontend in Magento 2.3.0+

2.1.2
=============
* Fixed:
    * Session write race condition for checkout modals

2.1.1
=============
* New:
    * Implemented address verification for autorization transaction Status Inquiry API response

* Fixed:
    * Fatal error while validating authorization status inquiry response with exception status code

2.1.0
=============
* New: 
    * Synchrony Installment payment method
    * Marketing functionality and content for Revolving Payment
    * Improved cart eligibility validation messaging 
    
2.0.9
=============
* Added support for PHP 7.3
    
2.0.8
=============
* New: 
    * Added use Proxy API configuration settings
    
2.0.7
=============
* New:
    * product restrictions (by attribute set)
* Fixed:
    * bundle products compatibility

2.0.6
=============
* Switched to utilize Digital Buy "framework free" merchant.js

2.0.5
=============
* Removed header and footer from pages with modals

2.0.4
=============
* Eased up PHP vesion constraints in composer.json to include 7.0.x

2.0.3
=============
* Fixed:
    * improved security around tokens
    * added work around for zip code field name case mismatch in status inquiry response
    * "product subselection" promotion condition not working
    * typo in notice message on promotions grid page

2.0.2
=============
* Fixed:
    * removed redundant "Content-Length" headers from API calls
    * typo in notice message on promotions grid page

2.0.1
=============
* Updated Synchrony logo

2.0.0
=============
* Initial release
