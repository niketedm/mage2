<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;

use Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position;
use Magento\Framework\App\ResourceConnection;

class GetMaxSortOrder
{
    const DEFAULT_SORT_ORDER = 99999;

    /**
     * @var int|null
     */
    private $maxSortOrder;

    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        ResourceConnection $resource
    ) {
        $this->resource = $resource;
    }

    public function execute(): int
    {
        if ($this->maxSortOrder === null) {
            $order = sprintf('%s DESC', Position::POSITION);
            $select = $this->resource->getConnection()->select()
                ->from(
                    [$this->resource->getTableName(Position::TABLE)],
                    [Position::POSITION]
                )
                ->order($order);

            $this->maxSortOrder = (int)$this->resource->getConnection()->fetchOne($select)
                ?: self::DEFAULT_SORT_ORDER;
        }

        return ++$this->maxSortOrder;
    }
}
