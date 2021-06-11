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

class InlineEdit extends \Magento\Backend\App\Action
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
    	$items = $this->getRequest()->getParam('items');
    	
    	if ($items)
    	{
    		foreach ($items as $id => $item)
    		{
    			try 
    			{
	    			$entity = $this->factory->create()->load($id);
	    			
	    			$entity->setFaq($item['faq']);
	    			$entity->setFaqContent($item['faq_content']);
	    			$entity->setFaqPosition($item['faq_position']);
	    			
	    			$entity->save();
    			}
    			catch (\Exception $e)
    			{
    				
    			}
    		}
    	}
    	$this->getResponse()->setBody(json_encode(['success' => true], JSON_PRETTY_PRINT));
    }
}