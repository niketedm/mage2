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



namespace Mirasvit\GdprConsent\Block;

use Magento\Framework\View\Element\Template;
use Mirasvit\GdprConsent\Model\ConfigProvider;

class ConsentCheckbox extends Template
{
    protected $_template = 'Mirasvit_GdprConsent::consent_checkbox.phtml';

    private   $configProvider;

    private   $type;

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider = $configProvider;
    }

    public function getText()
    {
        return $this->configProvider->getConsentCheckboxText();
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function _toHtml()
    {
        if (!$this->configProvider->isConsentCheckboxEnabled($this->type)) {
            return false;
        }

        return parent::_toHtml();
    }
}
