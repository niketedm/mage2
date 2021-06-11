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

class StatusSource implements OptionSourceInterface
{
    public function toOptionArray()
    {
        return [
            [
                'label' => __('Pending'),
                'value' => RequestInterface::STATUS_PENDING,
            ],
            [
                'label' => __('Processing'),
                'value' => RequestInterface::STATUS_PROCESSING,
            ],
            [
                'label' => __('Completed'),
                'value' => RequestInterface::STATUS_COMPLETED,
            ],
            [
                'label' => __('Partially completed'),
                'value' => RequestInterface::STATUS_PARTIALLY_COMPLETED,
            ],
            [
                'label' => __('Rejected'),
                'value' => RequestInterface::STATUS_REJECTED,
            ],
        ];
    }
}
