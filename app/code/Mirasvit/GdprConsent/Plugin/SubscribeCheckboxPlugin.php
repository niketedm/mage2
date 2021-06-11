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



namespace Mirasvit\GdprConsent\Plugin;

use Magento\Framework\View\Layout;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;
use Mirasvit\GdprConsent\Block\ConsentCheckbox;

/**
 * @see \Magento\Newsletter\Block\Subscribe
 */
class SubscribeCheckboxPlugin
{
    private $layout;

    public function __construct(
        Layout $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param \Magento\Newsletter\Block\Subscribe $subject
     * @param string                              $html
     *
     * @return string
     */
    public function afterToHtml($subject, $html)
    {
        /** @var ConsentCheckbox $checkbox */
        $checkbox = $this->layout->createBlock(ConsentCheckbox::class);
        $checkbox->setType(ConsentInterface::TYPE_SUBSCRIPTION);

        $pattern = '<div class="actions">';

        $html = str_replace($pattern, $checkbox->toHtml() . $pattern, $html);

        return $html;
    }
}
