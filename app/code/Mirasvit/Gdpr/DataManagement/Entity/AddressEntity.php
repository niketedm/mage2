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

use Magento\Customer\Model\Address;
use Magento\Sales\Api\Data\OrderAddressInterface;
use Magento\Sales\Model\ResourceModel\Order\CollectionFactory as OrderCollectionFactory;
use Magento\Customer\Model\ResourceModel\Address\Collection as AddressCollection;
use Mirasvit\Gdpr\Api\Data\EntityInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\DataManagement\Anonymizer\Anonymizer;
use Mirasvit\Gdpr\DataManagement\Entity\Helper\DataProtection;
use Mirasvit\Gdpr\Model\ConfigProvider;
use Magento\Sales\Model\Order\AddressRepository;

class AddressEntity implements EntityInterface
{
    private $orderCollectionFactory;

    private $configProvider;

    private $anonymizer;

    private $addressCollection;

    private $addressRepository;

    private $dataProtection;

    public function __construct(
        AddressCollection $addressCollection,
        AddressRepository $addressRepository,
        OrderCollectionFactory $orderCollectionFactory,
        Anonymizer $anonymizer,
        ConfigProvider $configProvider,
        DataProtection $dataProtection
    ) {
        $this->orderCollectionFactory = $orderCollectionFactory;
        $this->configProvider         = $configProvider;
        $this->anonymizer             = $anonymizer;
        $this->addressCollection      = $addressCollection;
        $this->addressRepository      = $addressRepository;
        $this->dataProtection         = $dataProtection;
    }

    public function getLabel()
    {
        return 'Addresses';
    }

    public function getCode()
    {
        return 'addresses';
    }

    public function provideData(CustomerInterface $customer)
    {
        $addresses = $customer->getAddresses();
        $data      = [];

        /** @var \Magento\Customer\Model\Data\Address $address */
        foreach ($addresses as $idx => $address) {
            $addressData = $address->__toArray();
            foreach ($this->configProvider->getAddressAttributes() as $attribute) {
                $data['address ' . $idx . ' ' . $attribute] = isset($addressData[$attribute]) ? $addressData[$attribute] : '';
            }
        }

        return $data;
    }

    public function removeData(CustomerInterface $customer)
    {
        $isAddressProtected = false;

        $customerAddressCollection = $this->addressCollection->setCustomerFilter($customer);

        /**
         * @var Address $address
         */
        foreach ($customerAddressCollection as $address) {
            if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $address->getData('created_at'))) {
                $isAddressProtected = true;
            } else {
                $address->delete();
            }
        }

        if ($isAddressProtected === true) {
            throw new \Exception('Customer Address protected');
        }

        return true;
    }

    public function anonymizeData(CustomerInterface $customer)
    {
        $orders             = $this->orderCollectionFactory->create()
            ->addFieldToFilter('customer_id', $customer->getId());
        $isAddressProtected = false;

        $customerAddressCollection = $this->addressCollection->setCustomerFilter($customer);
        foreach ($customerAddressCollection as $address) {
            if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $address->getData('created_at'))) {
                $isAddressProtected = true;
            } else {
                $this->anonymizeAddress($address);
            }
        }

        /** @var \Magento\Sales\Model\Order $order */
        foreach ($orders as $order) {
            $shippingAddressId = $order->getShippingAddress()->getData('customer_address_id');
            $billingAddressId  = $order->getBillingAddress()->getData('customer_address_id');

            $addressesCollection = $this->addressCollection->setCustomerFilter($customer);
            $addresses           = $addressesCollection
                ->addFieldToFilter('entity_id', ['in' => [$shippingAddressId, $billingAddressId]]);

            foreach ($addresses as $address) {
                if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $address->getData('created_at'))) {
                    $isAddressProtected = true;
                } else {
                    $shippingOrBilling = $shippingAddressId === $address->getData('entity_id') ?
                        $order->getShippingAddress() : $order->getBillingAddress();
                    $this->anonymizeOrderAddress($shippingOrBilling);
                }
            }
        }

        if ($isAddressProtected === true) {
            throw new \Exception('Customer Order is protected and can`t be anonymized');
        }
    }

    /**
     * @param OrderAddressInterface|false $address
     */
    private function anonymizeOrderAddress($address)
    {
        if (!$address) {
            return;
        }

        foreach ($this->configProvider->getAddressAttributes() as $attribute) {
            $address->setData($attribute, $this->anonymizer->getAnonymizeValue($attribute));
        }

        $this->addressRepository->save($address);
    }

    /**
     * @param Address $address
     *
     * @throws \Exception
     */
    private function anonymizeAddress($address)
    {
        if (!$address) {
            return;
        }

        foreach ($this->configProvider->getAddressAttributes() as $attribute) {
            $address->setData($attribute, $this->anonymizer->getAnonymizeValue($attribute));
        }

        $address->save();
    }

    public function validate(CustomerInterface $customer)
    {
        return true;
    }
}
