<?php
/**
 * Anowave Magento 2 Extra Fee
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Fee\Model\System\Config\Source;

class Render implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Hide fee in header
     * 
     * @var integer
     */
    const SHOW_NONE = 0;
    
    /**
     * Show fee in header 
     * 
     * @var integer
     */
    const SHOW_ALL = 1;
    
    /**
     * Show fee in header to logged users only
     * 
     * @var integer
     */
    const SHOW_LOGGED = 2;
    
    /**
     * Get options
     * 
     * @return array
     */
    public function toOptionArray() : array
    {
        return 
        [
            [
                'label' => __('No'),
                'value' => static::SHOW_NONE
            ],
            [
                'label' => __('Yes'),
                'value' => static::SHOW_ALL
            ],
            [
                'label' => __('Yes (logged only)'),
                'value' => static::SHOW_LOGGED
            ],
        ];
    }
}