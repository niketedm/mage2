<?php
/**
 * Authorize.net Payment Module.
 *
 * @category  Payment Integration
 * @package   Rootways_Authorizecim
 * @author    Developer RootwaysInc <developer@rootways.com>
 * @copyright 2021 Rootways Inc. (https://www.rootways.com)
 * @license   Rootways Custom License
 * @link      https://www.rootways.com/pub/media/extension_doc/license_agreement.pdf
 */

namespace Rootways\Authorizecim\Block\System\Config;

use Magento\Framework\Registry;

class Heading extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Registry
     */
    protected $_coreRegistry;
    
    /**
     * @var \Rootways\Authorizecim\Helper\Data
     */
    protected $helper;
    
    /**
     * @param Magento\Backend\Block\Template\Context $context
     * @param Rootways\Authorizecim\Helper\Data $helper
     * @param Registry $coreRegistry
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Rootways\Authorizecim\Helper\Data $helper,
        Registry $coreRegistry,
        array $data = []
    ) {
        $this->_coreRegistry = $coreRegistry;
        $this->_helper = $helper;
        parent::__construct($context, $data);
    }
    
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $base = $this->getBaseUrl();
        $html = '';
        $status = base64_decode($this->_helper->act());
        $a = base64_decode('RXh0ZW5zaW9uIEFjdGl2YXRlZC4=');
        if ($status != '') {
        $html .= <<<HTML
        <div style="margin-top: 5px;color: red;">$status</div>
HTML;
    } else {
            $html .= <<<HTML
        <div style="margin-top:  7px;color: #3bb53b;font-weight: bold;">$a</div>
HTML;
        }
        return $html;
    }
}
