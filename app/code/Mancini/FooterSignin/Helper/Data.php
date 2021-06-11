<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\FooterSignin\Helper;

use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
    /**
     * @var CustomerSession
     */
    private $_customerContext;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Magento\Framework\App\Http\Context $customerContext
     */
    public function __construct(
        \Magento\Framework\App\Http\Context $customerContext,
        \Magento\Framework\App\Helper\Context $context
    ) {
        $this->_customerContext = $customerContext;
        parent::__construct($context);
    }

    /**
     * Function to check whether customer logged in
     * @return string
     */
    public function isCustomerLoggedIn(){
        $isLoggedIn = $this->_customerContext->getValue(\Magento\Customer\Model\Context::CONTEXT_AUTH);
        
        return $isLoggedIn;
    }
}