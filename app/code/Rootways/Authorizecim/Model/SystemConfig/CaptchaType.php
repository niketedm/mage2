<?php
/**
 * Authorize.net Model AbstractMethod.
 *
 * @category  Payment Integration
 * @package   Rootways_Authorizecim
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */
 
namespace Rootways\Authorizecim\Model\SystemConfig;

use Magento\Payment\Model\Method\AbstractMethod;

class CaptchaType implements \Magento\Framework\Option\ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 0,
                'label' => __('Disabled'),
            ],
            /*
            [
                'value' => 1,
                'label' => __('reCAPTCHA v2 ("I am not a robot")'),
            ],
            [
                'value' => 2,
                'label' => __('reCAPTCHA v2 Invisible'),
            ],
            */
            [
                'value' => 3,
                'label' => __('reCAPTCHA v3 Invisible (Recommended)'),
            ]
        ];
    }
}
