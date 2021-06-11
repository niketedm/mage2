<?php
/**
 * Class to fetch information of installed module version.
 */
namespace Synchrony\DigitalBuy\Block\Adminhtml\System\Config\Form;

use Magento\Backend\Block\Template\Context;
use Synchrony\DigitalBuy\Gateway\Config\CommonConfig as Config;

class Version extends \Magento\Config\Block\System\Config\Form\Field
{
    /**
     * @var Config
     */
    private $config;

    /**
     * Version constructor.
     * @param Context $context
     * @param Config $config
     * @param array $data
     */
    public function __construct(
        Context $context,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
    }

    /**
     * Render element value
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    public function render(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $html = '<td class="label"><label><span>' .
            $element->getLabel() .
            '</span></label></td>';
        $html .= $this->_renderValue($element);
        return $this->_decorateRowHtml($element, $html);
    }

    /**
     * Retrieve element HTML markup
     *
     * @param \Magento\Framework\Data\Form\Element\AbstractElement $element
     * @return string
     */
    protected function _getElementHtml(\Magento\Framework\Data\Form\Element\AbstractElement $element)
    {
        $version = $this->config->getModuleVersion();
        if (!$version) {
            $version = __('--');
        }

        return '<div id="' . $element->getHtmlId() . '">' . $version . '</div>';
    }
}
