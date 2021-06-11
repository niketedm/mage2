<?php

namespace Mancini\ContactUs\Block;

use Magento\Framework\View\Element\Template;
use Zend_Validate_Exception;

class Fields extends Template
{
    /** @var Config */
    protected $_config;
    // protected $_template = 'Mancini_ContactUs::form.phtml';

    /**
     * @param Template\Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct( Template\Context $context)
    {
        parent::__construct($context);
    }

    /**
     * Returns action url for contact form
     *
     * @return array
     * @throws Zend_Validate_Exception
     */
    public function getServiceEmails()
    {
        return '';
    }
}
