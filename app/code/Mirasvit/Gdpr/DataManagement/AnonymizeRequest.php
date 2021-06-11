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

use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Service\RequestService;

class AnonymizeRequest implements RequestInstanceInterface
{
    private $requestService;

    private $entityRepository;

    public function __construct(
        EntityRepository $entityRepository,
        RequestService $requestService
    ) {
        $this->entityRepository = $entityRepository;
        $this->requestService   = $requestService;
    }

    public function validate(CustomerInterface $customer)
    {
        foreach ($this->entityRepository->getList() as $entity) {
            $entity->validate($customer);
        }
    }

    public function create(CustomerInterface $customer)
    {
        return $this->requestService->create($customer, RequestInterface::TYPE_ANONYMIZE);
    }

    public function canProcess(RequestInterface $request)
    {
        return true;
    }

    public function process(RequestInterface $request)
    {
        $customer    = $this->requestService->getCustomer($request);
        $isCompleted = true;
        foreach ($this->entityRepository->getList() as $entity) {
            try {
                $entity->anonymizeData($customer);
            } catch (\Exception $e) {
                $isCompleted = false;
            }
        }

        if ($isCompleted === false) {
            $this->requestService->partiallyCompleted($request);
        } else {
            $this->requestService->process($request);
        }

        return true;
    }

    public function deny(RequestInterface $request)
    {
        throw new \Exception('Method is not implemented');
    }
}
