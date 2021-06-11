<?php
/**
 * Anowave Magento 2 FAQ
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

class Create extends \Magento\Backend\App\Action
{
	/**
	 * Item Factory 
	 * 
	 * @var \Anowave\Faq\Model\ItemFactory
	 */
	protected $factory = null;

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Backend\App\Action\Context $context
	 * @param \Anowave\Faq\Model\ItemFactory $factory
	 */
	public function __construct
	(
		\Magento\Backend\App\Action\Context $context,
		\Anowave\Faq\Model\ItemFactory $factory
	)
	{
		parent::__construct($context);
		
		$this->factory = $factory;
	}
	
    public function execute()
    {
    	$response = new \Magento\Framework\DataObject(array
    	(
    		'success' => false
    	));
    	
    	$model = $this->factory->create();
    	
    	$model->setData(
    	[
    		'faq_product_id' 		=> (int) 	$this->getRequest()->getParam('faq_product_id'),
    		'faq_store_id'			=> (int)	$this->getRequest()->getParam('faq_store_id'),
    		'faq' 					=> 			$this->getRequest()->getParam('faq'),
    		'faq_content' 			=> 			$this->getRequest()->getParam('faq_content'),
    		'faq_enable'			=> 	true == filter_var($this->getRequest()->getParam('faq_enable'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ? 1 : 0
    	]);
    	
    	/**
    	 * Calculate position
    	 *
    	 * @var int $position
    	 */
    	$position = $this->getPosition($model);
    	
    	/**
    	 * Set position
    	 */
    	$model->setFaqPosition($position);
    	
    	try 
    	{
    		$model->save();
    		
    		$response->setSuccess(true);
    	}
    	catch (\Exception $e)
    	{
    		$response->setError($e->getMessage());
    	}
    	
    	$this->getResponse()->setBody(json_encode($response->getData(), JSON_PRETTY_PRINT));
    }
    
    /**
     * Calculate position 
     * 
     * @param \Anowave\Faq\Model\Item $model
     */
    private function getPosition(\Anowave\Faq\Model\Item $model)
    {
    	$sql = 'SELECT 
					(MAX(faq_position)+1) as position 
						FROM ' . $model->getResource()->getTable('ae_faq') . ' 
						WHERE 
							faq_product_id = ' . $model->getFaqProductId();
    	
    	$position = (int) $model->getResource()->getConnection()->fetchOne($sql);
    	
    	return $position;
    }
}