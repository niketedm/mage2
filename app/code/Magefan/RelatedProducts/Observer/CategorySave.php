<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 *
 * Glory to Ukraine! Glory to the heroes!
 */

namespace Magefan\RelatedProducts\Observer;

use Magento\Framework\Event\ObserverInterface;

/**
 * DEPRECATED
 *
 * Blog observer
 */
class CategorySave implements ObserverInterface
{
    /**
     * @var \Magento\Framework\App\Cache\TypeListInterface
     */
    protected $_typeList;

    /**
     * Application config object
     *
     * @var \Magento\PageCache\Model\Config
     */
    protected $_config;

    /**
     * @param \Magento\PageCache\Model\Config $config
     * @param \Magento\Framework\App\Cache\TypeListInterface $typeList
     */
    public function __construct(
        \Magento\PageCache\Model\Config $config,
        \Magento\Framework\App\RequestInterface $request,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\Cache\TypeListInterface $typeList,
        \Magento\SalesRule\Model\RuleFactory  $ruleFactory
        ) {
        $this->_request = $request;
        $this->_logger = $logger;
        $this->_config = $config;
        $this->_typeList = $typeList;
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Invalidate full page and block cache
     *
     * @param \Magento\Framework\Event\Observer $observer
     * @return void
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute(\Magento\Framework\Event\Observer $observer)
    {


        try {
            // Get the model object from observer
            $model = $observer->getEvent()->getDataObject();

            $relatedProducts = serialize($this->_request->getPost('blog_related_product_listing'));
            $model->setData('related_products',$relatedProducts);


            if ($model->getRule('conditions')) {
                $rule = $this->ruleFactory->create();
                $rule->loadPost(['conditions' => $model->getRule('conditions')]);
                $rule->beforeSave();
                $model->setData(
                    'rp_conditions_serialized',
                    $rule->getConditionsSerialized()
                );
            }
            //$model->save();
            } catch (\Exception $e) {
        }



    }
}
