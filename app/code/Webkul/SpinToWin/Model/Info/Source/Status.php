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

namespace Webkul\SpinToWin\Model\Info\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class Status
 */
class Status implements OptionSourceInterface
{
    /**
     * Get options
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = [];
        $options[] = ['label' => 'Disabled', 'value' => 0];
        $options[] = ['label' => 'Enabled', 'value' => 1];
        return $options;
    }
}
