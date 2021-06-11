<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GdprConsent\Ui\Consent\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;

class TypeSource implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Cookies'),
                'value' => ConsentInterface::TYPE_COOKIE,
            ],
            [
                'label' => __('Registration Form'),
                'value' => ConsentInterface::TYPE_REGISTRATION,
            ],
            [
                'label' => __('Contact Us Form'),
                'value' => ConsentInterface::TYPE_CONTACT_US,
            ],
            [
                'label' => __('Subscription 1 Form'),
                'value' => ConsentInterface::TYPE_SUBSCRIPTION,
            ],
            [
                'label' => __('Checkout Form'),
                'value' => ConsentInterface::TYPE_CHECKOUT,
            ],
        ];
    }
}
