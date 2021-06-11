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



namespace Mirasvit\LayeredNavigation\Block\Adminhtml\Config\Source;

use Magento\Backend\Block\Template\Context;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Mirasvit\LayeredNavigation\Service\ElasticsearchService;
use Mirasvit\LayeredNavigation\Service\VersionService;

class ElasticFilterCountFix extends \Magento\Config\Block\System\Config\Form\Field
{

    /**
     * @var ElasticsearchService
     */
    private $elasticsearchService;
    /**
     * @var VersionService
     */
    private $versionService;

    /**
     * ElasticFilterCountFix constructor.
     * @param Context $context
     * @param VersionService $versionService
     * @param ElasticsearchService $elasticsearchService
     * @param array $data
     */
    public function __construct(
        Context $context,
        VersionService $versionService,
        ElasticsearchService $elasticsearchService,
        array $data = []
    ) {
        $this->versionService       = $versionService;
        $this->elasticsearchService = $elasticsearchService;
        parent::__construct($context, $data);
    }

    /**
     * @param AbstractElement $element
     *
     * @return string
     */
    public function render(AbstractElement $element)
    {
        if ($this->versionService->isEe() && $this->elasticsearchService->isElasticEnabled()) {
            return parent::render($element);
        }

        return false;
    }
}
