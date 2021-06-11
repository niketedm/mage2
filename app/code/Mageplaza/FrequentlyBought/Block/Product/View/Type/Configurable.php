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
 * @category  Mageplaza
 * @package   Mageplaza_FrequentlyBought
 * @copyright Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license   https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FrequentlyBought\Block\Product\View\Type;

/**
 * Class Configurable
 *
 * @package Mageplaza\FrequentlyBought\Block\Product\View\Type
 */
class Configurable extends \Magento\Swatches\Block\Product\Renderer\Configurable
{
    const MAGEPLAZA_FREQUENTLY_BOUGHT_RENDERER_TEMPLATE = 'Mageplaza_FrequentlyBought::product/view/type/options/configurable.phtml';

    /**
     * @return string
     */
    protected function getRendererTemplate()
    {
        return self::MAGEPLAZA_FREQUENTLY_BOUGHT_RENDERER_TEMPLATE;
    }
}
