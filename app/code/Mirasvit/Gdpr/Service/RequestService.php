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



namespace Mirasvit\Gdpr\Service;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Repository\RequestRepository;

class RequestService
{
    private $requestRepository;

    private $customerRepository;

    public function __construct(
        RequestRepository $requestRepository,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->requestRepository  = $requestRepository;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param CustomerInterface $customer
     * @param string            $action
     *
     * @return RequestInterface
     */
    public function create(CustomerInterface $customer, $action)
    {
        $request = $this->requestRepository->create();
        $request->setCustomerId($customer->getId())
            ->setType($action)
            ->setStatus(RequestInterface::STATUS_PENDING);

        return $this->requestRepository->save($request);
    }

    public function process(RequestInterface $request)
    {
        $request->setStatus(RequestInterface::STATUS_COMPLETED);

        return $this->requestRepository->save($request);
    }

    public function processing(RequestInterface $request)
    {
        $request->setStatus(RequestInterface::STATUS_PROCESSING);

        return $this->requestRepository->save($request);
    }

    public function partiallyCompleted(RequestInterface $request)
    {
        $request->setStatus(RequestInterface::STATUS_PARTIALLY_COMPLETED);

        return $this->requestRepository->save($request);
    }

    public function deny(RequestInterface $request)
    {
        $request->setStatus(RequestInterface::STATUS_REJECTED);

        return $this->requestRepository->save($request);
    }

    /**
     * @param RequestInterface $request
     *
     * @return CustomerInterface
     * @throws \Exception
     */
    public function getCustomer(RequestInterface $request)
    {
        return $this->customerRepository->getById($request->getCustomerId());
    }

    public function unsetCustomer(CustomerInterface $customer)
    {
        $collection = $this->requestRepository->getCollection();
        $collection->addFieldToFilter(RequestInterface::CUSTOMER_ID, $customer->getId());

        foreach ($collection as $request) {
            $request->setCustomerId(null);

            $this->requestRepository->save($request);
        }
    }

    /**
     * @param CustomerInterface $customer
     * @param string            $requestType
     *
     * @return false|RequestInterface
     */
    public function findRequest(CustomerInterface $customer, $requestType)
    {
        $collection = $this->requestRepository->getCollection();
        $collection->addFieldToFilter(RequestInterface::CUSTOMER_ID, $customer->getId());
        $collection->addFieldToFilter(RequestInterface::TYPE, $requestType);

        if ($collection->getSize()) {
            return $collection->getFirstItem();
        }

        return false;
    }
}
