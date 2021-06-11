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



namespace Mirasvit\GdprCookie\Block;

use Magento\Framework\View\Element\Template;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Model\ConfigProvider;
use Mirasvit\GdprCookie\Model\ResourceModel\CookieGroup\CollectionFactory as GroupCollectionFactory;

class SettingsModal extends Template
{
    private $groupCollectionFactory;

    private $configProvider;

    public function __construct(
        GroupCollectionFactory $groupCollectionFactory,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->configProvider         = $configProvider;
        $this->groupCollectionFactory = $groupCollectionFactory;
    }

    public function _toHtml()
    {
        if (!$this->configProvider->isCookieBarEnabled()) {
            return false;
        }

        return parent::_toHtml();
    }

    public function getPopupTitle()
    {
        return $this->configProvider->getCookieModalTitle();
    }

    public function getPopupDescription()
    {
        return $this->configProvider->getCookieModalText();
    }

    public function getCookieGroups()
    {
        $storeId = $this->_storeManager->getStore()->getId();

        return $this->groupCollectionFactory->create()
            ->addFilterByIsActive(true)
            ->addFilterByStoreId($storeId)
            ->addOrderByPriority();
    }

    public function getCookieGroupHtml(CookieGroupInterface $group)
    {
        /** @var \Mirasvit\GdprCookie\Block\CookieGroupBlock $groupBlock */
        $groupBlock = $this->getLayout()->createBlock('\Mirasvit\GdprCookie\Block\CookieGroupBlock', 'cookie-group-' . $group->getId());

        $groupBlock->setCookieGroup($group);

        return $groupBlock->toHtml();
    }
}
