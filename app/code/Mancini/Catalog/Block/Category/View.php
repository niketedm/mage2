<?php

/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mancini\Catalog\Block\Category;

/**
 * Class View
 * @api
 * @package Magento\Catalog\Block\Category
 * @since 100.0.2
 */
class View extends \Magento\Catalog\Block\Category\View
{

    /**
     * @return mixed
     */
    public function getCmsBlockHtml()
    {
        if (!$this->getData('cms_block_html')) {
            $html = $this->getBlockContent();
            $this->setData('cms_block_html', $html);
        }
        return $this->getData('cms_block_html');
    }

   

    public function getBlockContent()
    {
        $activeFilters = $this->_catalogLayer->getState()->getFilters();


        $filterLabel = "";
        $final_value = "";
        $filterCount = count($activeFilters);
        if ($filterCount == 1) {
            foreach ($activeFilters as $filter) {
                $filterLabel = strtolower($filter->getLabel());
                $filterValue = $filter->getValue();
                $filterLabel = rtrim($filterLabel, ' ');
                $filterValue = str_replace(' ','_',$filterLabel);
                $filterIdentifier = 'filter_block_'.$filterValue;
            }
           
            $html = $this->getLayout()->createBlock(
                \Magento\Cms\Block\Block::class
            )->setBlockId(
                $filterIdentifier
            )->toHtml();
            
            if($html == ''){
                return $this->getLayout()->createBlock(
                    \Magento\Cms\Block\Block::class
                )->setBlockId(
                    $this->getCurrentCategory()->getLandingPage()
                )->toHtml();
            }else{
                return $html;
            }

        } else {
            return $this->getLayout()->createBlock(
                \Magento\Cms\Block\Block::class
            )->setBlockId(
                $this->getCurrentCategory()->getLandingPage()
            )->toHtml();
        }
    }

}
