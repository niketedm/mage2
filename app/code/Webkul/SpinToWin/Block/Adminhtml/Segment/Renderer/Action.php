<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Block\Adminhtml\Segment\Renderer;

class Action extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\AbstractRenderer
{
    /**
     * Segment Edit
     *
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $category = $row->getDataAllowed();
        if (empty($category)) {
            $value =  $row->getData($this->getColumn()->getIndex());
            return '<a href="#" class="spin-segment-edit-action" data-id="'.$row->getId().'">Edit</a>';
        }
    }
}
