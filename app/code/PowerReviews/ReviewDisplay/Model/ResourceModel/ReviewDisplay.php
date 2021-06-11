<?php
/**
 * Copyright © 2017 PowerReviews. All rights reserved.
 */

namespace PowerReviews\ReviewDisplay\Model\ResourceModel;

class ReviewDisplay extends \Magento\Framework\Model\ResourceModel\Db\AbstractDb
{
    /**
     * Model Initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('powerreviews_reviewdisplay', 'id');
    }
}
