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



namespace Mirasvit\Gdpr\DataManagement\Entity;

use Magento\Sales\Model\Order;
use Magento\Sales\Model\OrderRepository;
use Mirasvit\Gdpr\Api\Data\EntityInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Mirasvit\Gdpr\DataManagement\Anonymizer\Anonymizer;
use Mirasvit\Gdpr\DataManagement\Entity\Helper\DataProtection;
use Mirasvit\Gdpr\Model\ConfigProvider;

class OrderEntity implements EntityInterface
{
    private $orderCollectionFactory;

    private $orderRepository;

    private $configProvider;

    private $anonymizer;

    private $dataProtection;

    public function __construct(
        OrderCollectionFactory $orderCollectionFactory,
        OrderRepository $orderRepository,
        Anonymizer $anonymizer,
        ConfigProvider $configProvider,
        DataProtection $dataProtection
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->anonymizer             = $anonymizer;
        $this->orderRepository        = $orderRepository;
        $this->configProvider         = $configProvider;
        $this->dataProtection         = $dataProtection;
    }

    public function getLabel()
    {
        return 'Order';
    }

    public function getCode()
    {
        return 'order';
    }

    public function provideData(CustomerInterface $customer)
    {
        $data   = [];
        $orders = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            $orderData = $order->getData();
            foreach ($this->configProvider->getOrderAttributes() as $attribute) {
                $data['order ' . $order->getIncrementId() . ' ' . $attribute] = isset($orderData[$attribute]) ? $orderData[$attribute] : '';
            }
        }

        return $data;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return bool
     * @throws \Exception
     */
    public function removeData(CustomerInterface $customer)
    {
        return false;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return bool|void
     * @throws \Exception
     */
    public function anonymizeData(CustomerInterface $customer)
    {
        $orders           = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());
        $isOrderProtected = false;

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $order->getData('created_at'))) {
                $isOrderProtected = true;
            } else {
                foreach ($this->configProvider->getOrderAttributes() as $attribute) {
                    $order->setData($attribute, $this->anonymizer->getAnonymizeValue($attribute));
                }
            }

            $this->orderRepository->save($order);
        }
        if ($isOrderProtected === true) {
            throw new \Exception('Customer Order protected and can`t be anonymized');
        }
    }

    public function validate(CustomerInterface $customer)
    {
        $collection = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId())
            ->addFieldToFilter('state', ['in' => [Order::STATE_NEW, Order::STATE_PROCESSING]]);

        if ($collection->getSize() === 0) {
            return;
        }

        $ids = [];
        /** @var \Magento\Sales\Model\Order $order */
        foreach ($collection as $order) {
            $ids[] = $order->getIncrementId();
        }
        $ids = implode(',', $ids);

        throw new \Exception(__("We can't process the request right now because you have pending order(s): %1", $ids));
    }
}
