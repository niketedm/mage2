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
 * @package   mirasvit/module-seo
 * @version   2.1.2
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Seo\Plugin\Frontend;

use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\UrlRewrite\Controller\Router;
use Magento\UrlRewrite\Model\UrlFinderInterface;
use Magento\UrlRewrite\Model\UrlRewrite;
use Mirasvit\Seo\Model\Config;

/**
 * @see \Magento\UrlRewrite\Controller\Router::match();
 */
class UrlRewriteRouterApplyTrailingSlashPlugin
{
    const ADD_SLASH     = 'add';
    const REMOVE_SLASH  = 'remove';

    private $config;

    private $urlRewrite;

    private $urlFinder;

    private $storeManager;

    public function __construct(
        Config $config,
        UrlRewrite $urlRewrite,
        UrlFinderInterface $urlFinder,
        StoreManagerInterface $storeManager
    ) {
        $this->config       = $config;
        $this->urlRewrite   = $urlRewrite;
        $this->urlFinder    = $urlFinder;
        $this->storeManager = $storeManager;
    }

    public function aroundMatch(Router $subject, callable $proceed, RequestInterface $request)
    {
        $rewriteUrl = '';

        if ($this->config->getTrailingSlash() == Config::TRAILING_SLASH_DISABLE) {
            return $proceed($request);
        } else if ($this->config->getTrailingSlash() == Config::NO_TRAILING_SLASH) {
            $rewriteUrl = trim($request->getPathInfo(), '/');
            $rewrite = $this->getRewrite(
                $rewriteUrl,
                $this->storeManager->getStore()->getId()
            );

            if (!empty($rewrite) && $rewrite->getUrlRewriteId()) {
                return $proceed($request);
            } else {
                $this->updateUrlRewrite($request, $rewriteUrl, self::REMOVE_SLASH);
            }

        } else {
            $rewriteUrl = trim($request->getPathInfo(), '/') .'/';
            $rewrite = $this->getRewrite(
                $rewriteUrl,
                $this->storeManager->getStore()->getId()
            );

            if (!empty($rewrite) && $rewrite->getUrlRewriteId()) {
                return $proceed($request);
            } else {
                $this->updateUrlRewrite($request, $rewriteUrl, self::ADD_SLASH);
            }
        }

        return $proceed($request);
    }

    private function updateUrlRewrite(RequestInterface $request, string $rewriteUrl, string $action)
    {
        $requestPath = $request->getPathInfo();
        if ($action == self::ADD_SLASH) {
            $rewriteUrl = $rewriteUrl .'/';
            $requestPath = trim($request->getPathInfo(), '/') .'/';
        } else {
            $rewriteUrl = trim($rewriteUrl, '/');
            $requestPath = trim($request->getPathInfo(), '/');
        }

        $rewrite = $this->getRewrite(
            $rewriteUrl,
            $this->storeManager->getStore()->getId()
        );

        if (!empty($rewrite) && $rewrite->getUrlRewriteId()) {
            try {
                $rewrite = $this->urlRewrite
                    ->setStoreId($this->storeManager->getStore()->getId())
                    ->load($rewrite->getUrlRewriteId())
                    ->setData('request_path', $requestPath)
                    ->save();
            } catch (\Exception $e) {}
        }
    }

    /**
     * @param string   $requestPath
     * @param int|null $storeId
     *
     * @return \Magento\UrlRewrite\Service\V1\Data\UrlRewrite|null
     */
    protected function getRewrite($requestPath, $storeId)
    {
        return $this->urlFinder->findOneByData(
            [
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::REQUEST_PATH => ltrim($requestPath, '/'),
                \Magento\UrlRewrite\Service\V1\Data\UrlRewrite::STORE_ID     => $storeId,
            ]
        );
    }
}
