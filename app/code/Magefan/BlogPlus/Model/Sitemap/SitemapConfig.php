<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\Sitemap;

use Magefan\Blog\Api\SitemapConfigInterface;

/**
 * Class SitemapConfig
 * @package Magefan\BlogPlus\Model\Sitemap
 */
class SitemapConfig extends \Magefan\Blog\Model\Config implements SitemapConfigInterface
{
    /**
     * @param string $page
     * @return bool
     */
    public function isEnabledSitemap($page)
    {
        return $this->getValue($page, 'enabled');
    }

    /**
     * @param string $page
     * @return string
     */
    public function getFrequency($page)
    {
        return $this->getValue($page, 'frequency');
    }

    /**
     * @param string $page
     * @return float
     */
    public function getPriority($page)
    {
        return $this->getValue($page, 'priority');
    }

    /**
     * @param $page
     * @param $type
     * @return mixed
     */
    public function getValue($page, $type)
    {
        return $this->getConfig('mfblog/sitemap/' . $page . '/' . $type);
    }
}
