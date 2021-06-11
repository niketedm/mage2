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



namespace Mirasvit\Gdpr\Ui\Request\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;

class TypeSource implements OptionSourceInterface
{

    public function toOptionArray()
    {
        return [
            [
                'label' => __('Provide User Data'),
                'value' => RequestInterface::TYPE_PROVIDE_DATA,
            ],
            [
                'label' => __('Anonymize User Data'),
                'value' => RequestInterface::TYPE_ANONYMIZE,
            ],
            [
                'label' => __('Remove User Data'),
                'value' => RequestInterface::TYPE_REMOVE,
            ],
        ];
    }
}