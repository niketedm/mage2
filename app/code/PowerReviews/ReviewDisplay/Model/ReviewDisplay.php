<?php
/**
 * Copyright © 2017 PowerReviews. All rights reserved.
 */

namespace PowerReviews\ReviewDisplay\Model;

class ReviewDisplay extends \Magento\Framework\Model\AbstractModel
{
    /**
     * Constructor
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->_init('PowerReviews\ReviewDisplay\Model\ResourceModel\ReviewDisplay');
    }
}
