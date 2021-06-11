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



namespace Mirasvit\Gdpr\Model\Notification;

use Magento\Framework\Notification\MessageInterface;
use Magento\Framework\UrlInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Repository\RequestRepository;

class ActionRequiredMessage implements MessageInterface
{
    private $urlBuilder;

    private $requestRepository;

    public function __construct(
        UrlInterface $urlBuilder,
        RequestRepository $requestRepository
    ) {
        $this->urlBuilder        = $urlBuilder;
        $this->requestRepository = $requestRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function isDisplayed()
    {
        return $this->hasPendingRequests() ? true : false;
    }

    /**
     * {@inheritdoc}
     */
    public function getIdentity()
    {
        return hash("sha256", __CLASS__);
    }

    /**
     * {@inheritdoc}
     */
    public function getSeverity()
    {
        return self::SEVERITY_MAJOR;
    }

    /**
     * {@inheritdoc}
     */
    public function getText()
    {
        $text = '';

        if ($this->hasPendingRequests()) {
            $url  = $this->urlBuilder->getUrl('mst_gdpr/request/index');
            $text .= __('GDPR: One or more <a href="%1">customers\' requests</a> are currently in pending status.', $url);
        }

        return $text;
    }

    /**
     * @return bool
     */
    private function hasPendingRequests()
    {
        $collection = $this->requestRepository->getCollection();
        $collection->addFieldToFilter(RequestInterface::STATUS, RequestInterface::STATUS_PENDING);

        return $collection->getSize() ? true : false;
    }
}
