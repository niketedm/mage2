<?php
namespace Anowave\Faq\Controller\Adminhtml\Index;

class MassReorder extends \Magento\Backend\App\Action
{
	protected $factory = null;

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
		foreach ((array) $this->getRequest()->getParam('faq') as $faq)
		{
			/**
			 * Reorder FAQ
			 */
		}
		
		$this->getResponse()->setBody(json_encode(array
		(
			'success' => true
		), JSON_PRETTY_PRINT));
	}
}
