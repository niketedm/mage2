<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Plugin\Model\ResourceModel;

/**
 * Class PostUpdateRelatedProductsPlugin
 * @package Magefan\BlogPlus\Plugin\Model\ResourceModel
 */
class PostUpdateRelatedProductsPlugin
{
    /**
     * @var \Magefan\BlogPlus\Model\RelatedProductsRule
     */
    public $relatedProductsRule;

    /**
     * PostUpdateRelatedProductsPlugin constructor.
     * @param \Magefan\BlogPlus\Model\RelatedProductsRule $relatedProductsRule
     */
    public function __construct(
        \Magefan\BlogPlus\Model\RelatedProductsRule $relatedProductsRule
    ) {
        $this->relatedProductsRule = $relatedProductsRule;
    }

    /**
     * Upldate related products using rule conditions
     * @param \Magefan\Blog\Model\ResourceModel\Post $subject
     * @param callable $proceed
     * @param $post
     * @return mixed
     */
    public function aroundSave(
        \Magefan\Blog\Model\ResourceModel\Post $subject,
        callable $proceed,
        $post
    ) {
        $result = $proceed($post);
        $this->relatedProductsRule->updateRelatedProducts($post);
        return $result;
    }
}
