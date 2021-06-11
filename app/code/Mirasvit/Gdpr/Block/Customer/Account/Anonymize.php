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



namespace Mirasvit\Gdpr\Block\Customer\Account;

use Magento\Framework\View\Element\Template;
use Mirasvit\Gdpr\Model\ConfigProvider;

class Anonymize extends Template
{
    private $configProvider;

    public function __construct(
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider = $configProvider;
    }

    public function _toHtml()
    {
        if (!$this->configProvider->isDataAnonymizeEnabled()) {
            return false;
        }

        return parent::_toHtml();
    }
}