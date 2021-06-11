<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SpinToWin\Observer;

use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\Stdlib\CookieManagerInterface;
use Magento\Framework\Session\SessionManagerInterface;

class CustomerDataClear implements \Magento\Framework\Event\ObserverInterface
{
    public function __construct(
        \Magento\Framework\Json\Helper\Data $jsonData,
        CookieMetadataFactory $cookieMetadata,
        CookieManagerInterface $cookieManager,
        SessionManagerInterface $sessionManager
    ) {
        $this->jsonData = $jsonData;
        $this->cookieMetadata = $cookieMetadata;
        $this->cookieManager = $cookieManager;
        $this->sessionManager = $sessionManager;
    }

    /**
     * Main
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        $metadata = $this->cookieMetadata->createPublicCookieMetadata()
                            ->setDuration(-3600)
                            ->setPath($this->sessionManager->getCookiePath())
                            ->setDomain($this->sessionManager->getCookieDomain());
        $this->cookieManager->setPublicCookie(
            'spintowin_spins',
            $this->jsonData->jsonEncode("[]"),
            $metadata
        );

        $this->cookieManager->setPublicCookie(
            'spintowin_coupon',
            $this->jsonData->jsonEncode("[]"),
            $metadata
        );
    }
}
