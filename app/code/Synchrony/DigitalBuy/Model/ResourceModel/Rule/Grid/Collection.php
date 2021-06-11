<?php

namespace Synchrony\DigitalBuy\Model\ResourceModel\Rule\Grid;

/**
 * Synchrony Promotion Rules resource collection model
 *
 */
class Collection extends \Synchrony\DigitalBuy\Model\ResourceModel\Rule\Collection
{
    /**
     * Add websites to select
     *
     * @return $this
     */
    public function _initSelect()
    {
        parent::_initSelect();
        $this->addWebsitesToResult();
        return $this;
    }
}
