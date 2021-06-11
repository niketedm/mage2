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



namespace Mirasvit\GdprConsent\Plugin;

use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResponseInterface;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;
use Mirasvit\GdprConsent\Service\ConsentService;

/**
 * @see \Magento\Framework\App\ActionInterface
 */
class ConsentProcessorPlugin
{
    private $request;

    private $consentService;

    public function __construct(
        RequestInterface $request,
        ConsentService $consentService
    ) {
        $this->request        = $request;
        $this->consentService = $consentService;
    }

    /**
     * @param ActionInterface   $subject
     * @param ResponseInterface $response
     *
     * @return ResponseInterface
     */
    public function afterDispatch($subject, $response)
    {
        $action = $this->request->getFullActionName();

        if ($action === 'checkout_onepage_success') {
            $this->consentService->submitPermission(ConsentInterface::TYPE_CHECKOUT);

            return $response;
        }

        $isConsented = $this->request->getParam('is_consented');
        if (!$isConsented) {
            return $response;
        }

        if ($action === 'customer_account_createpost') {
            $this->consentService->submitPermission(ConsentInterface::TYPE_REGISTRATION);
        } elseif ($action === 'newsletter_subscriber_new') {
            $this->consentService->submitPermission(ConsentInterface::TYPE_SUBSCRIPTION);
        } elseif ($action === 'contact_index_post') {
            $this->consentService->submitPermission(ConsentInterface::TYPE_CONTACT_US);
        }

        return $response;
    }
}
