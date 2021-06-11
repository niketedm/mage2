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



namespace Mirasvit\Gdpr\Cron;

use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory as CustomerCollectionFactory;
use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\App\ResourceConnection;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Model\ConfigProvider;
use Mirasvit\Gdpr\Service\RequestService;

class AutoRemoveCron
{
    /**
     * How many customers we select for processing
     * @var int
     */
    const LIMIT_CUSTOMER = 1000;

    /**
     * Limit for processing remove request
     * @var int
     */
    const LIMIT_REMOVE_REQUESTS = 100;

    private $customerCollectionFactory;
    private $orderCollectionFactory;
    private $configProvider;
    private $requestService;
    private $customerRepository;
    /**
     * @var ResourceConnection
     */
    private $resource;

    public function __construct(
        CustomerCollectionFactory $customerFactory,
        OrderCollectionFactory $orderFactory,
        ConfigProvider $configProvider,
        RequestService $requestService,
        ResourceConnection $resource,
        CustomerRepository $customerRepository
    )
    {
        $this->customerCollectionFactory = $customerFactory;
        $this->orderCollectionFactory = $orderFactory;
        $this->configProvider = $configProvider;
        $this->resource = $resource;
        $this->requestService = $requestService;
        $this->customerRepository = $customerRepository;
    }

    public function execute()
    {
        if (!$this->configProvider->isAutoRemoveEnabled()) {
            return;
        }

        $page = 1;
        $addedRemoveRequestsCounter = 0;
        $daysToAutoRemove = $this->configProvider->daysToAutoRemove();
        $daysLeft = date("Y-m-d", strtotime("-" . $daysToAutoRemove . " days"));

        while ($customerIds = $this->getExpiredCustomerIdsWithoutRemoveRequest($daysLeft, $page, self::LIMIT_CUSTOMER)) {
            foreach ($customerIds as $customerId) {
                if (!$this->isExistRecentOrder($customerId, $daysLeft)) {
                    $this->requestService
                        ->create($this->customerRepository->getById($customerId), RequestInterface::TYPE_REMOVE);
                    $addedRemoveRequestsCounter++;
                    if ($addedRemoveRequestsCounter === self::LIMIT_REMOVE_REQUESTS) {
                        break;
                    }
                }
            }
            $page++;
        }
    }

    /**
     * @param int $customerId
     * @param string $daysLeft
     *
     * @return bool
     */
    private function isExistRecentOrder($customerId, $daysLeft)
    {
        $recentNewOrder = $this->orderCollectionFactory->create($customerId)
            ->addFieldToFilter('created_at', ['gteq' => $daysLeft])
            ->setPageSize(1)
            ->setOrder('created_at', 'desc')
            ->fetchItem();
        return $recentNewOrder === false ? false : true;
    }

    /**
     * @param string $daysLeft - date() format
     * @param int $page
     * @param int $rowCount
     * @return array
     */
    private function getExpiredCustomerIdsWithoutRemoveRequest($daysLeft, $page, $rowCount)
    {
        $select = $this->resource->getConnection()->select()
            ->from(['customer' => $this->resource->getTableName('customer_entity')], ['customer.entity_id'])
            ->joinLeft(
                ['request' => $this->resource->getTableName(RequestInterface::TABLE_NAME)],
                "request.customer_id = customer.entity_id AND request.type = 'remove'"
            )
            ->where('request.request_id IS NULL')
            ->where('customer.created_at <= ?', $daysLeft)
            ->limitPage($page, $rowCount);

        return $this->resource->getConnection()->fetchCol($select);
    }
}
