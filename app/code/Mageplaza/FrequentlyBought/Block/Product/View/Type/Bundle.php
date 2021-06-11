<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     ${MODULENAME}
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FrequentlyBought\Block\Product\View\Type;

use Magento\Bundle\Block\Catalog\Product\View\Type\Bundle as CatalogBundle;

/**
 * Class Bundle
 * @package Mageplaza\FrequentlyBought\Block\Product\View\Type
 */
class Bundle extends CatalogBundle
{
    /**
     * @return array
     */
    public function getBundleOptions()
    {
        $this->options = null;

        return $this->getOptions();
    }
}
