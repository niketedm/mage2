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



namespace Mirasvit\GdprCookie\Ui\CookieGroup\Form\Control;

use Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;

/**
 * Class BackButton
 */
class DeleteButton extends GenericButton implements ButtonProviderInterface
{

    /**
     * @return array
     */
    public function getButtonData()
    {
        $data = [];

        if ($this->getId()) {
            $data = [
                'label'      => __('Delete Cookie Group'),
                'class'      => 'delete',
                'on_click'   => 'deleteConfirm(\'' . __(
                        'Are you sure you want to do this?'
                    ) . '\', \'' . $this->getDeleteUrl() . '\')',
                'sort_order' => 20,
            ];
        }

        return $data;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->context->getRequest()->getParam(CookieGroupInterface::ID);
    }

    /**
     * @return string
     */
    public function getDeleteUrl()
    {
        return $this->getUrl('*/*/delete', [CookieGroupInterface::ID => $this->getId()]);
    }
}
