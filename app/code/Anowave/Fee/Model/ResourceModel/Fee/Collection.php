<?php
/**
 * Anowave Magento 2 Extra Fee
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
 
namespace Anowave\Fee\Model\ResourceModel\Fee;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{
	protected $_idFieldName = 'fee_id';
	
    /**
     * Initialize resource
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('Anowave\Fee\Model\Fee', 'Anowave\Fee\Model\ResourceModel\Fee');
    }
    
    /**
     * Get collection size 
     * 
     * {@inheritDoc}
     * @see \Magento\Framework\Data\Collection\AbstractDb::getSize()
     */
    public function getSize()
    {
    	return sizeof($this->getAllIds());
    }
    
    /**
     * Get distinct values 
     * 
     * @return \Anowave\Price\Model\ResourceModel\Item\Collection
     */
    public function addDistinctCount()
    {
    	return $this;
    }
    
    /**
     * After load processing
     *
     * @return $this
     */
    protected function _afterLoad()
    {
    	parent::_afterLoad();

    	return $this;
    }
}