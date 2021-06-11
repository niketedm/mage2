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
 * @package   mirasvit/module-navigation
 * @version   1.1.0
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Service;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Search\Model\EngineResolver;

class ElasticsearchService
{
    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;
    /**
     * @var EngineResolver
     */
    private $engineResolver;

    /**
     * ElasticsearchService constructor.
     * @param EngineResolver $engineResolver
     * @param ScopeConfigInterface $scopeConfig
     */
    public function __construct(
        EngineResolver $engineResolver,
        ScopeConfigInterface $scopeConfig
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->engineResolver = $engineResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isElasticEnabled()
    {
        if ($this->scopeConfig->getValue('catalog/search/engine') === 'elasticsearch') {
            return true;
        }

        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function isElasticEngineUsed()
    {
        return strpos($this->engineResolver->getCurrentSearchEngine(), 'elastic') !== false;
    }
}
