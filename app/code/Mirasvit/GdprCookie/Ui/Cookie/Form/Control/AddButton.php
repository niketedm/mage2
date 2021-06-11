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



namespace Mirasvit\GdprCookie\Ui\Cookie\Form\Control;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;

/**
 * Class AddButton
 */
class AddButton extends GenericButton implements ButtonProviderInterface
{
    /**
     * @return array
     */
    public function getButtonData()
    {
        return [
            'label'      => __('Add Cookie'),
            'on_click'   => sprintf("location.href = '%s';", $this->getUrl('gdpr_cookie/cookie/edit')),
            'class'      => 'save primary',
            'sort_order' => 10,
        ];
    }

    /**
     * Get URL for back (reset) button
     * @return string
     */
    public function getBackUrl()
    {
        return $this->getUrl('*/*/');
    }
}
