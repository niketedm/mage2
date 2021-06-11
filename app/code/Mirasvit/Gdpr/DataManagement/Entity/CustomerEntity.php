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

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Mirasvit\Gdpr\Api\Data\EntityInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\DataManagement\Anonymizer\Anonymizer;
use Mirasvit\Gdpr\Model\ConfigProvider;
use Mirasvit\Gdpr\DataManagement\Entity\Helper\DataProtection;
use Mirasvit\Gdpr\Service\RequestService;

class CustomerEntity implements EntityInterface
{
    private $configProvider;

    private $requestService;

    private $customerRepository;

    private $anonymizer;

    private $dataProtection;

    public function __construct(
        CustomerRepository $customerRepository,
        RequestService $requestService,
        Anonymizer $anonymizer,
        ConfigProvider $configProvider,
        DataProtection $dataProtection
    ) {
        $this->customerRepository = $customerRepository;
        $this->configProvider     = $configProvider;
        $this->anonymizer         = $anonymizer;
        $this->requestService     = $requestService;
        $this->dataProtection     = $dataProtection;
    }

    public function getLabel()
    {
        return 'Customer';
    }

    public function getCode()
    {
        return 'customer';
    }

    public function provideData(CustomerInterface $customer)
    {
        $customerData = $this->getCustomerData($customer);

        $data = [];
        foreach ($this->configProvider->getCustomerAttributes() as $attribute) {
            $data['account ' . $attribute] = isset($customerData[$attribute]) ? $customerData[$attribute] : '';
        }

        return $data;
    }

    public function removeData(CustomerInterface $customer)
    {
        $customerData        = $this->getCustomerData($customer);
        $isCustomerProtected = false;

        if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $customerData['created_at'])) {
            $isCustomerProtected = true;
        } else {
            $this->anonymizeData($customer);
            $this->customerRepository->delete($customer);
            $this->requestService->unsetCustomer($customer);
        }

        if ($isCustomerProtected === true) {
            throw new \Exception('Customer Data protected and can`t be removed');
        }

        return true;
    }

    /**
     * @param CustomerInterface $customer
     *
     * @return bool|void
     * @throws \Exception
     */
    public function anonymizeData(CustomerInterface $customer)
    {
        $customerData        = $this->getCustomerData($customer);
        $isCustomerProtected = false;

        if ($this->dataProtection->isDataProtectedByDay($this->getCode(), $customerData['created_at'])) {
            $isCustomerProtected = true;
        } else {
            foreach ($this->configProvider->getCustomerAttributes() as $attribute) {
                $value = $this->anonymizer->getAnonymizeValue($attribute);
                /** @var \Magento\Customer\Model\Data\Customer $customer */
                $customer->setData($attribute, $value);
            }
        }

        $this->customerRepository->save($customer);

        if ($isCustomerProtected === true) {
            throw new \Exception('Customer Data protected and can`t be anonymized');
        }
    }

    /**
     * @param object $customer
     *
     * @return array
     */
    private function getCustomerData($customer)
    {
        return $customer->__toArray();
    }

    public function validate(CustomerInterface $customer)
    {
        return true;
    }
}
