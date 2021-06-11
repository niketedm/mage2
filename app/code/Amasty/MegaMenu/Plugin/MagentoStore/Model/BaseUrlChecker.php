<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Plugin\MagentoStore\Model;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\PageCache\Model\Config as FpcConfig;

/**
 * Fix Magento bug related with esi block rendering issue with ssl enabled
 */
class BaseUrlChecker
{
    const PAGE_CACHE_ESI_RENDER_IDENTIFIER_URL_PART = '/page_cache/block/esi';

    /**
     * @var FpcConfig
     */
    private $cacheConfig;

    public function __construct(
        FpcConfig $cacheConfig
    ) {
        $this->cacheConfig = $cacheConfig;
    }

    /**
     * Return true when only url schema differs
     *
     * @param \Magento\Store\Model\BaseUrlChecker $subject
     * @param bool $result
     * @param array $uri
     * @param HttpRequest $request
     *
     * @return bool
     */
    public function afterExecute(
        \Magento\Store\Model\BaseUrlChecker $subject,
        bool $result,
        array $uri,
        $request
    ): bool {
        if (!$result
            && $this->cacheConfig->getType() == FpcConfig::VARNISH
            && strpos($request->getRequestUri(), self::PAGE_CACHE_ESI_RENDER_IDENTIFIER_URL_PART) === 0
        ) {
            $requestUri = $request->getRequestUri() ? $request->getRequestUri() : '/';
            $isValidHost = !isset($uri['host']) || $uri['host'] === $request->getHttpHost();
            $isValidPath = !isset($uri['path']) || strpos($requestUri, (string) $uri['path']) !== false;

            return $isValidHost && $isValidPath;
        }

        return $result;
    }
}
