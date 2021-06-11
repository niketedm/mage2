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

use Mirasvit\Gdpr\Repository\RequestRepository;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\DataManagement\RemoveRequest;
use Mirasvit\Gdpr\DataManagement\AnonymizeRequest;

class HandleRequestsCron
{
    private $removeRequest;

    private $anonymizeRequest;

    private $requestRepository;

    public function __construct(
        RequestRepository $requestRepository,
        RemoveRequest $removeRequest,
        AnonymizeRequest $anonymizeRequest
    ) {
        $this->requestRepository = $requestRepository;
        $this->removeRequest     = $removeRequest;
        $this->anonymizeRequest  = $anonymizeRequest;
    }

    public function execute()
    {
        $requests = $this->requestRepository->getCollection()
            ->addFieldToFilter('status', [
                ['eq' => RequestInterface::STATUS_PARTIALLY_COMPLETED],
                ['eq' => RequestInterface::STATUS_PROCESSING],
            ]);

        foreach ($requests as $request) {
            if ($request->getType() === RequestInterface::TYPE_ANONYMIZE) {
                $this->anonymizeRequest->process($request);
            } elseif ($request->getType() === RequestInterface::TYPE_REMOVE) {
                $this->removeRequest->process($request);
            }
        }
    }
}
