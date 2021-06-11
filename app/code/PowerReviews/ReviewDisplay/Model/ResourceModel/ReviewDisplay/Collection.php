<?php
/**
 * Copyright © 2017 PowerReviews. All rights reserved.
 */

namespace PowerReviews\ReviewDisplay\Model\ResourceModel\ReviewDisplay;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
    /**
     * Define resource model
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('PowerReviews\ReviewDisplay\Model\ReviewDisplay', 'PowerReviews\ReviewDisplay\Model\ResourceModel\ReviewDisplay');
    }
}
