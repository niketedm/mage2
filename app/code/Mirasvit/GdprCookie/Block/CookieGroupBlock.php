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
use Mirasvit\GdprCookie\Model\Cookie;
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie\Collection;
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie\CollectionFactory;

class CookieGroupBlock extends Template
{
    /**
     * @var Collection|null
     */
    private $collection = null;

    /**
     * @var CookieGroupInterface
     */
    private $group;

    protected $_template = 'Mirasvit_GdprCookie::modal/group.phtml';

    private $collectionFactory;

    private $configProvider;

    public function __construct(
        CollectionFactory $collectionFactory,
        ConfigProvider $configProvider,
        Template\Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);

        $this->collectionFactory = $collectionFactory;
        $this->configProvider    = $configProvider;
    }

    public function setCookieGroup(CookieGroupInterface $group)
    {
        $this->group = $group;

        return $this;
    }

    public function getCookieGroup()
    {
        return $this->group;
    }

    /**
     * @return array
     */
    public function getCookies()
    {
        $cookies = [];

        /** @var Cookie $cookie */
        foreach ($this->getCookieCollection() as $cookie) {
            if ($cookie->getGroupId() == $this->getCookieGroup()->getId()) {
                $cookies[] = $cookie;
            }
        }

        return $cookies;
    }

    private function getCookieCollection()
    {
        if (!$this->collection) {
            $this->collection = $this->collectionFactory->create();
        }

        return $this->collection;
    }
}
