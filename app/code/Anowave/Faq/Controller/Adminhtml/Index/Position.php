<?php
/**
 * Anowave Magento 2 Frequently Asked Questions
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
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Faq\Controller\Adminhtml\Index;

use Magento\Framework\Controller\ResultFactory;

abstract class Position extends \Magento\Backend\App\Action
{
	/**
	 * @var \Anowave\Faq\Model\ItemFactory
	 */
	protected $factory = null;
	
	/**
	 * @var \Magento\Framework\App\ResourceConnection
	 */
	protected $resource;
	
	/**
	 * @var \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory
	 */
	protected $collectionFactory;
	
	/**
	 * Connection
	 */
	private $connection = null;

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Magento\Framework\App\ResourceConnection $resource
	 * @param \Anowave\Faq\Model\ItemFactory $factory
	 */
	public function __construct
	(
		\Magento\Backend\App\Action\Context $context,
		\Magento\Framework\App\ResourceConnection $resource,
		\Anowave\Faq\Model\ItemFactory $factory,
		\Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory
	)
	{
		parent::__construct($context);
		
		/**
		 * Set factory 
		 * 
		 * @var \Anowave\Faq\Controller\Adminhtml\Index\Sort $factory
		 */
		$this->factory = $factory;
		
		/**
		 * Set resource
		 * 
		 * @var \Anowave\Faq\Controller\Adminhtml\Index\Sort $resource
		 */
		$this->resource = $resource;
		
		/**
		 * Set collection factory
		 * 
		 * @var \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory
		 */
		$this->collectionFactory = $collectionFactory;
	}
	
	protected function getConnection()
	{
		if (!$this->connection)
		{
			$this->connection = $this->resource->getConnection(\Magento\Framework\App\ResourceConnection::DEFAULT_CONNECTION);
		}
		
		return $this->connection;
	}
	
	/**
	 * Get request id 
	 * 
	 * @return number
	 */
	protected function getEntityId()
	{
		return (int) $this->getRequest()->getParam('id');
	}

	/**
	 * Redirect back
	 * 
	 * @return \Magento\Framework\Controller\ResultInterface
	 */
	protected function redirectBack()
	{
		$resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
		
		$resultRedirect->setUrl($this->_redirect->getRefererUrl());
		
		return $resultRedirect;
	}
	
	protected function getModel()
	{
		$model = $this->factory->create();
		
		if ($this->getEntityId())
		{
			$model->load
			(
				$this->getEntityId()
			);
		}
		
		return $model;
	}
	
	/**
	 * Swap sort positions
	 * 
	 * @param number $prev
	 * @param number $next
	 * @return boolean
	 */
	protected function swap($prev, $next)
	{
		$sql = 'UPDATE
				' . $this->getConnection()->getTableName('ae_faq') . ' AS t1,
				' . $this->getConnection()->getTableName('ae_faq') . ' AS t2
			    SET
			   		t1.faq_position = (@tmp:=t1.faq_position), t1.faq_position = t2.faq_position,
			   		t2.faq_position = @tmp
		  		WHERE t1.faq_id = ' . $prev . ' AND t2.faq_id = ' . (int) $next;
		
		$this->getConnection()->query($sql);
		
		return true;
	}
}
