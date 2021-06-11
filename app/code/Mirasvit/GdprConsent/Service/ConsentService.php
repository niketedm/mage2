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



namespace Mirasvit\GdprConsent\Service;

use Magento\Customer\Model\Session as CustomerSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;
use Mirasvit\GdprConsent\Repository\ConsentRepository;

class ConsentService
{
    private $consentRepository;

    private $customerSession;

    private $remoteAddress;

    public function __construct(
        ConsentRepository $consentRepository,
        CustomerSession $customerSession,
        RemoteAddress $remoteAddress
    ) {
        $this->consentRepository = $consentRepository;
        $this->customerSession   = $customerSession;
        $this->remoteAddress     = $remoteAddress;
    }

    /**
     * @param string $type
     *
     * @return ConsentInterface
     */
    public function submitPermission($type)
    {
        $customerId = $this->customerSession->getCustomerId();

        $request = $this->consentRepository->create();
        $request->setCustomerId($customerId)
            ->setRemoteAddr($this->remoteAddress->getRemoteAddress())
            ->setType($type)
            ->setStatus(ConsentInterface::STATUS_ALLOW);

        return $this->consentRepository->save($request);
    }
}
