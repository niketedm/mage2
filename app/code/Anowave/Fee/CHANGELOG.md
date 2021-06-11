# Changelog

All notable changes to this project will be documented in this file.

## [100.1.4]

### Added

- Added ability to show/hide cart fee header for logged and non-logged customers

## [100.1.3]

### Fixed

- Fixed wrong quantity calculation

## [100.1.2]

### Added

- Added "Calculate per product" in separate fees (Sales -> Fees -> Edit fee)

## [100.1.1]

### Changed

- Changed fee to allow 4 decimal places

## [100.1.0]

### Fixed

- Fixed wrong rule being applied to all products in cart (when calculate per product is checked)

## [100.0.9]

### Fixed

- Removed wrong fee tax applied for non-taxable shipping destinations

## [100.0.8]

### Added

- Added ability to apply fee for logged only customers/guest customers or group-specific customers

## [100.0.7]

### Added

- Added ability to show fee next to product list in cart (See Stores -> Configuration -> Anowave Extensions -> Extra fee -> Fee options -> Show fee in cart header)

## [100.0.6]

### Added

- Added \app\code\Anowave\Fee\view\frontend\layout\sales_email_order_items.xml (Fee row in order confirmation email)


## [100.0.5]

### Fixed

- Added default fee name for stores with empty fee name

## [100.0.4]

### Fixed

- Minor updates in Plugin/Update.php

## [100.0.3]

### Fixed

- Fixed Infinite loop cauused in Observer/Taxt.php

## [100.0.2]

### Added

- Added 'fee' to Order API response

## [100.0.1]

### Fixed

- Fixed a fatal error with tax fee

## [5.0.4]

### Fixed

- Small compatibility updates (Magento 2.3.x)

## [5.0.3]

### Fixed

- Changed fee_type column from ENUM to VARCHAR(3)

## [5.0.2]

### Fixed

- Fixed install table prefix

## [5.0.1]

### Fixed

- Fixed a but related to not multiplying fee by product quantity when using Fixed fee per category + Calculate by product + Multiply by quantity

## [5.0.0]

### Fixed

- Fixed an error Fatal error: Method Magento\Ui\TemplateEngine\Xhtml\Result::__toString() must not throw an exception, caught ArgumentCountError: Too few arguments to function Anowave\Fee\Block\Config\ConditionsTemplate::setConditionFormName()

## [4.0.9]

### Fixed

- Fixed a broken attribute picker in fee conditions/rules

## [4.0.8]

### Fixed

- Minor updates related to multiple fees
- Added fee tax row

## [4.0.7]

### Fixed

- Monor updates

## [4.0.6]

### Fixed

- Fixed wrong "Multiply by quantity" calculation for multiple fees

## [4.0.5]

### Fixed

- Fixed wrong conditions applied (multi-store confguration)
- Fixed wrong "Multiply by quantity" calculation for general fee

## [4.0.4]

### Fixed

- Added conditions check for global fee based on product calculation

## [4.0.3]

### Added

- Added a tax row related to tax applied on fee

## [4.0.2]

### Fixed

- Added multi-store support for global rule conditions (fee)

## [4.0.1]

### Fixed

- Fixed conditions fee based on state/region

## [4.0.0]

### Fixed

- Removed incorrect argument injection related to sales conditions rules

## [3.0.9]

### Fixed

- Broken sales rule actions conditions

## [3.0.8]

### Added

- None option for Product specific fee attribute

## [3.0.5]

### Added

- Taxable fee

## [3.0.4]

### Fixed

- Compilation issues

## [3.0.3]

### Added

- Ability to calculate category based fee for associated products (not assigned to category)

## [3.0.2]

### Fixed

- Conditions not working properly

## [3.0.1]

### Fixed

- PayPal issue with totals

## [3.0.0]

### Added

- Added fee row in Invoice pdf

## [2.0.9]

- Fixed wrong fee calculation on grand total instead of sub-total

## [2.0.8]

- Fixed fatal error on order view page

## [2.0.7]

- Fixed Magento 2.2 doubled fee

## [2.0.6]

- Magento 2.2 compatibility updates

## [2.0.5]

- Allowed for setting fee for simple products (in configurable)

## [2.0.4]

### Fixed

- Magento 2.2.x compatibility updates

## [2.0.3]

### Fixed

- Restored "Payment method" condition

## [1.0.0]

- Initial version