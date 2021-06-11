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



namespace Mirasvit\Seo\Observer\Admin;

use Magento\Framework\Event\ObserverInterface;
use Mirasvit\Seo\Model\Config\ProductUrlTemplateConfig;
use Mirasvit\Seo\Service\UrlTemplate\ProductUrlTemplateService;
use Magento\UrlRewrite\Model\ResourceModel\UrlRewriteCollection;
use Mirasvit\Seo\Model\Config as SeoConfig;

class UrlKeyChange implements ObserverInterface
{
    /**
     * @var \Magento\Framework\Message\ManagerInterface
     */
    private $messageManager;

    /**
     * @var ProductUrlTemplateConfig
     */
    private $productUrlTemplateConfig;

    /**
     * @var UrlRewriteCollection
     */
    private $urlRewriteCollection;

    /**
     * @var ProductUrlTemplateService
     */
    private $productUrlTemplateService;

    /**
     * @var \Mirasvit\Seo\Model\SeoObject\ProducturlFactory
     */
    protected $objectProducturlFactory;

    /**
     * @var SeoConfig
     */
    private $seoConfig;

    /**
     * ProductSave constructor.
     *
     * @param ProductUrlTemplateConfig                        $productUrlTemplateConfig
     * @param ProductUrlTemplateService                       $productUrlTemplateService
     * @param UrlRewriteCollection                            $urlRewriteCollection
     * @param \Mirasvit\Seo\Model\SeoObject\ProducturlFactory $objectProducturlFactory
     * @param \Magento\Framework\Message\ManagerInterface     $messageManager
     * @param SeoConfig                                       $seoConfig
     */
    public function __construct(
        ProductUrlTemplateConfig $productUrlTemplateConfig,
        ProductUrlTemplateService $productUrlTemplateService,
        \Mirasvit\Seo\Model\SeoObject\ProducturlFactory $objectProducturlFactory,
        UrlRewriteCollection $urlRewriteCollection,
        \Magento\Framework\Message\ManagerInterface $messageManager,
        SeoConfig $seoConfig
    ) {
        $this->productUrlTemplateConfig  = $productUrlTemplateConfig;
        $this->productUrlTemplateService = $productUrlTemplateService;
        $this->urlRewriteCollection      = $urlRewriteCollection;
        $this->objectProducturlFactory   = $objectProducturlFactory;
        $this->messageManager            = $messageManager;
        $this->seoConfig                 = $seoConfig;
    }

    /**
     * @param \Magento\Framework\Event\Observer $observer
     *
     * @return void
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product         = $observer->getEvent()->getProduct();
        $storeId         = $product->getStoreId();
        $urlKeyTemplates = $this->productUrlTemplateService->getUrlKeyTemplates();

        if ($product->isObjectNew() &&
            $this->seoConfig->isUrlKeyRewriteEnabled($storeId) &&
            $urlKeyTemplates
        ) {
            $rewriteCollection = $this->urlRewriteCollection
                ->addFieldToFilter('entity_type', 'product')
                ->addFieldToFilter('entity_id', $product->getId())
                ->addFieldToFilter('redirect_type', 0)
                ->addFieldToFilter('metadata', ['null' => true]);

            foreach ($this->productUrlTemplateService->processUrlRewriteCollection(
                $rewriteCollection,
                $urlKeyTemplates,
                false
            ) as $result) {
            }

            $this->addAdminNotification('URL key of this product has been modified by Mirasvit Advanced SEO Suite extension');
        }
    }

    /**
     * @param string $message
     */
    private function addAdminNotification($message)
    {
        $this->messageManager->addNoticeMessage($message);
    }
}
