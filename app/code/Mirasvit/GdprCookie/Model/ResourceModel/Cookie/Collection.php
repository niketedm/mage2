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



namespace Mirasvit\GdprCookie\Model\ResourceModel\Cookie;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager,
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
        $this->storeManager = $storeManager;
    }

    protected function _construct()
    {
        $this->_init(
            \Mirasvit\GdprCookie\Model\Cookie::class,
            Cookie::class
        );
    }

    /**
     * @param null|string|bool|int|Store $store
     *
     * @return $this
     */
    public function addStoreFilter($store = null)
    {
        if (is_null($store)) {
            $storeId = $this->storeManager->getStore()->getId();
        } else {
            if ($store instanceof Store) {
                $storeId = $store->getId();
            } else {
                $storeId = $store;
            }
        }

        $this->addFieldToFilter(CookieInterface::STORE_IDS, [
            ['finset' => 0],
            ['finset' => $storeId],
        ]);

        return $this;
    }
}
