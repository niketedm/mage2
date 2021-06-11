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



namespace Mirasvit\Gdpr\DataManagement;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Service\RequestService;

class RemoveRequest implements RequestInstanceInterface
{
    private $requestService;

    private $anonymizeRequest;

    private $customerRepository;

    private $entityRepository;

    public function __construct(
        RequestService $requestService,
        AnonymizeRequest $anonymizeRequest,
        CustomerRepositoryInterface $customerRepository,
        EntityRepository $entityRepository
    ) {
        $this->requestService     = $requestService;
        $this->anonymizeRequest   = $anonymizeRequest;
        $this->customerRepository = $customerRepository;
        $this->entityRepository   = $entityRepository;
    }

    /**
     * For Customer side
     */
    public function validate(CustomerInterface $customer)
    {
        $this->anonymizeRequest->validate($customer);

        $request = $this->requestService->findRequest($customer, RequestInterface::TYPE_REMOVE);

        $statusesForCheck = [
            RequestInterface::STATUS_PENDING,
            RequestInterface::STATUS_PROCESSING,
            RequestInterface::STATUS_PARTIALLY_COMPLETED,
        ];

        if ($request && in_array($request->getStatus(), $statusesForCheck)) {
            throw new \Exception(__("You already have a pending removal request."));
        }
    }

    public function create(CustomerInterface $customer)
    {
        return $this->requestService->create($customer, RequestInterface::TYPE_REMOVE);
    }

    public function canProcess(RequestInterface $request)
    {
        return false;
    }

    public function process(RequestInterface $request)
    {
        $customer = $this->requestService->getCustomer($request);

        $isCompleted = true;
        foreach ($this->entityRepository->getList() as $entity) {
            try {
                $entity->removeData($customer);
            } catch (\Exception $e) {
                $isCompleted = false;
            }
        }

        if ($isCompleted === false) {
            $this->requestService->partiallyCompleted($request);
        } else {
            $this->requestService->process($request);
        }
    }

    public function deny(RequestInterface $request)
    {
        $this->requestService->deny($request);
    }
}
