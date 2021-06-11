<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Plugin\Theme\Block\Html;

use Magento\Catalog\Model\Layer\Resolver as LayerResolver;
use Magento\Theme\Block\Html\Topmenu;

class TopmenuPlugin
{
    const PAGE_CACHE_ESI_ROUTE = 'page_cache/block/esi';

    /**
     * @var LayerResolver
     */
    private $layerResolver;

    public function __construct(LayerResolver $layerResolver)
    {
        $this->layerResolver = $layerResolver;
    }

    public function beforeGetUrl(Topmenu $subject, string $route, array $params): array
    {
        if ($route === self::PAGE_CACHE_ESI_ROUTE) {
            $catalogLayer = $this->layerResolver->get();
            if ($catalogLayer) {
                $currentCategory = $catalogLayer->getCurrentCategory();
                if ($currentCategory) {
                    $params['current_category'] = $currentCategory->getId();
                }
            }
        }

        return [$route, $params];
    }
}
