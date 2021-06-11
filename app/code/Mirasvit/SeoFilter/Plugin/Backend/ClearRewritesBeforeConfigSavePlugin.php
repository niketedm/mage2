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
 * @package   mirasvit/module-seo-filter
 * @version   1.1.3
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\SeoFilter\Plugin\Backend;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Mirasvit\SeoFilter\Api\Data\RewriteInterface;

/**
 * Clear rewrites, if rewrites configuration was re-saved
 * @see \Magento\Config\Model\Config::save()
 */
class ClearRewritesBeforeConfigSavePlugin
{
    private $resource;

    private $messageManager;

    public function __construct(
        ResourceConnection $resource,
        MessageManagerInterface $messageManager
    ) {
        $this->resource       = $resource;
        $this->messageManager = $messageManager;
    }

    /**
     * @param \Magento\Config\Model\Config $subject
     *
     * @return void
     */
    public function beforeSave($subject): void
    {
        $data = $subject->getData();

        if (isset($data['groups']['seofilter'])) {
            $connection = $this->resource->getConnection();
            $table      = $this->resource->getTableName(RewriteInterface::TABLE_NAME);
            $query      = 'TRUNCATE TABLE ' . $table;
            $connection->query($query);

            $this->messageManager->addNoticeMessage('Please refresh magento cache for apply changes.');
        }
    }
}
