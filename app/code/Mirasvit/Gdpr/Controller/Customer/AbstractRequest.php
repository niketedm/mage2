<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Gdpr\Controller\Customer;

use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Customer\Model\Authentication;
use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Response\Http\FileFactory;
use Mirasvit\Gdpr\DataManagement\AnonymizeRequest;
use Mirasvit\Gdpr\DataManagement\DataRequest;
use Mirasvit\Gdpr\DataManagement\RemoveRequest;

abstract class AbstractRequest extends Action
{
    private   $customerSession;

    private   $authentication;

    protected $anonymizeRequest;

    protected $dataRequest;

    protected $removeRequest;

    protected $fileFactory;

    public function __construct(
        CustomerSession $customerSession,
        Authentication $authentication,
        AnonymizeRequest $anonymizeRequest,
        DataRequest $dataRequest,
        RemoveRequest $removeRequest,
        FileFactory $fileFactory,
        Context $context
    ) {
        $this->customerSession = $customerSession;
        $this->authentication  = $authentication;

        $this->anonymizeRequest = $anonymizeRequest;
        $this->dataRequest      = $dataRequest;
        $this->removeRequest    = $removeRequest;

        $this->fileFactory = $fileFactory;

        parent::__construct($context);
    }

    protected function validate()
    {
        $customerId = $this->customerSession->getId();
        $password   = $this->getRequest()->getParam('password');

        try {
            $this->authentication->authenticate($customerId, $password);
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage('The password is wrong. Please enter correct password.');

            return false;
        }

        return true;
    }

    /**
     * @return CustomerInterface
     */
    protected function getCustomer()
    {
        return $this->customerSession->getCustomerData();
    }
}
