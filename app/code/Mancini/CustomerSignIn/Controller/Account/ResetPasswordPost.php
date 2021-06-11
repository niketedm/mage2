<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Mancini\CustomerSignIn\Controller\Account;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Customer;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Exception\InputException;
use Magento\Customer\Model\Customer\CredentialsValidator;

/**
 * Class ResetPasswordPost
 *
 * @package Magento\Customer\Controller\Account
 */
class ResetPasswordPost extends \Magento\Customer\Controller\AbstractAccount implements HttpPostActionInterface
{
    /**
     * @var \Magento\Customer\Api\AccountManagementInterface
     */
    protected $accountManagement;

    /**
     * @var \Magento\Customer\Api\CustomerRepositoryInterface
     */
    protected $customerRepository;

    /** 
    * @var \Magento\Customer\Model\Customer
    */
    protected $customerModel;

    /**
     * @var \Mancini\Frog\Helper\ApiCall 
     */
    protected $_apiCallHelper;

    /**
     * @var Session
     */
    protected $session;

    /**
     * @param Context $context
     * @param Session $customerSession
     * @param AccountManagementInterface $accountManagement
     * @param CustomerRepositoryInterface $customerRepository
     * @param CredentialsValidator|null $credentialsValidator
     * @param \Magento\Customer\Model\Customer $customerModel
     * @param \Mancini\Frog\Helper\ApiCall $_apiCallHelper
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function __construct(
        Context $context,
        Session $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerRepositoryInterface $customerRepository,
        CredentialsValidator $credentialsValidator = null,
        \Magento\Customer\Model\Customer $customerModel,
        \Mancini\Frog\Helper\ApiCall $_apiCallHelper

    ) {
        $this->session              = $customerSession;
        $this->accountManagement    = $accountManagement;
        $this->customerRepository   = $customerRepository;
        $this->customerModel        = $customerModel;
        $this->_apiCallHelper       = $_apiCallHelper;
        parent::__construct($context);
    }

    /**
     * Reset forgotten password
     *
     * Used to handle data received from reset forgotten password form
     *
     * @return \Magento\Framework\Controller\Result\Redirect
     */
    public function execute()
    {
        /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
        $resultRedirect         = $this->resultRedirectFactory->create();
        $resetPasswordToken     = (string)$this->getRequest()->getQuery('token');
     
        $emailData      = $this->customerModel->getCollection()->addFieldToFilter('rp_token',$resetPasswordToken)->getData();
        $email          = $emailData[0]['email'];
        $customerId     = $emailData[0]['entity_id'];
        $customerData   = $this->customerModel->load($customerId);
        $frogFirstToken = $customerData->getFrogtoken();

        $password               = (string)$this->getRequest()->getPost('password');
        $passwordConfirmation   = (string)$this->getRequest()->getPost('password_confirmation');

        //parameters to be passed to frog
        $params['user_name']    =   $email; 
        $params['token']        =   $frogFirstToken;
        $params['type']         =   "password";
        $params['password']     =   $password  ;

        $result     =   $this->_apiCallHelper->inetWebsiteResetPassword(\Zend_Http_Client::POST, $params);

        if ($password !== $passwordConfirmation) {
            $this->messageManager->addErrorMessage(__("New Password and Confirm New Password values didn't match."));
            $resultRedirect->setPath('*/*/createPassword', ['token' => $resetPasswordToken]);

            return $resultRedirect;
        }
        if (iconv_strlen($password) <= 0) {
            $this->messageManager->addErrorMessage(__('Please enter a new password.'));
            $resultRedirect->setPath('*/*/createPassword', ['token' => $resetPasswordToken]);

            return $resultRedirect;
        }

        try {
            $this->accountManagement->resetPassword(
                null,
                $resetPasswordToken,
                $password
            );
            $this->session->unsRpToken();
            $this->messageManager->addSuccessMessage(__('You updated your password.'));
            $resultRedirect->setPath('*/*/login');

            return $resultRedirect;
        } catch (InputException $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
            foreach ($e->getErrors() as $error) {
                $this->messageManager->addErrorMessage($error->getMessage());
            }
        } catch (\Exception $exception) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving the new password.'));
        }
        $resultRedirect->setPath('*/*/createPassword', ['token' => $resetPasswordToken]);

        return $resultRedirect;
    }
}
