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

namespace Anowave\Fee\Controller\Adminhtml\Index;

use Anowave\Fee\Model\Fee as Fee;

class Delete extends \Magento\Backend\App\Action
{
	public function execute()
	{
		$id = $this->getRequest()->getParam('id');
		
		if (!($fee = $this->_objectManager->create(Fee::class)->load($id))) 
		{
			$this->messageManager->addErrorMessage(__('Unable to proceed. Please, try again.'));
			
			$resultRedirect = $this->resultRedirectFactory->create();
			
			return $resultRedirect->setPath('*/*/index', array('_current' => true));
		}
		try
		{
			$fee->delete();
			
			$this->messageManager->addSuccessMessage(__('Fee deleted successfully'));
		} 
		catch (Exception $e) 
		{
			$this->messageManager->addErrorMessage(__('Error while trying to delete fee: '));
			
			$resultRedirect = $this->resultRedirectFactory->create();
			
			return $resultRedirect->setPath('*/*/index', array('_current' => true));
		}
		
		$resultRedirect = $this->resultRedirectFactory->create();
		
		return $resultRedirect->setPath('*/*/index', array('_current' => true));
	}
}