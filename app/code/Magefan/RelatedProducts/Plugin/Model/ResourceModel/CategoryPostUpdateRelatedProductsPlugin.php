<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\RelatedProducts\Plugin\Model\ResourceModel;

/**
 * Class PostUpdateRelatedProductsPlugin
 * @package Magefan\BlogPlus\Plugin\Model\ResourceModel
 */
class CategoryPostUpdateRelatedProductsPlugin
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
        \Magefan\RelatedProducts\Model\RelatedProductsRule $relatedProductsRule
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
        \Magefan\Blog\Model\ResourceModel\Category $subject,
        callable $proceed,
        $category
    ) {
        $result = $proceed($category);

        $this->relatedProductsRule->updateRelatedProducts($category);
        return $result;
    }
}
