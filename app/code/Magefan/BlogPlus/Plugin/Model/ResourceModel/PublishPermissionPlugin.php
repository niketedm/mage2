<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Plugin\Model\ResourceModel;

/**
 * Class PublishPermissionPlugin
 * @package Magefan\BlogPlus\Plugin\Model\ResourceModel
 */
abstract class PublishPermissionPlugin
{
    /**
     * @var \Magefan\BlogPlus\Plugin\Model\ResourceModel\PublishPermissionPlugin\Processor
     */
    protected $processor;

    /**
     * PublishPermissionPlugin constructor.
     * @param PublishPermissionPlugin\Processor $processor
     */
    public function __construct(
        PublishPermissionPlugin\Processor $processor
    ) {
        $this->processor = $processor;
    }
}
