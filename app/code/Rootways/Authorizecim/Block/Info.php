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

namespace Rootways\Authorizecim\Block;

use Magento\Framework\Phrase;
use Magento\Payment\Block\ConfigurableInfo;

/**
 * Class Info
 */
class Info extends ConfigurableInfo
{
    protected $_avsCode = array (
        'A' => 'The street address matched, but the postal code did not',
        'B' => 'No address information was provided',
        'E'=> 'The AVS check returned an error',
        'G'=> 'The card was issued by a bank outside the U.S. and does not support AVS',
        'N'=> 'Neither the street address nor postal code matched',
        'P'=> 'AVS is not applicable for this transaction',
        'R'=> ' s unavailable or timed out',
        'S'=> 'AVS is not supported by card issuer',
        'U'=> 'Address information is unavailable',
        'W'=> 'The US ZIP+4 code matches, but the street address does not',
        'X'=> 'Both the street address and the US ZIP+4 code matched',
        'Y'=> 'The street address and postal code matched',
        'Z'=> 'The postal code matched, but the street address did not.'
    );
    
    protected $_cvvCode = array (
        'M' => 'CVV matched',
        'N' => 'CVV did not match',
        'P' => 'CVV was not processed',
        'S' => 'CVV should have been present but was not indicated',
        'U' => 'The issuer was unable to process the CVV check'
    );
    
    /**
     * Returns label
     *
     * @param string $field
     * @return Phrase
     */
    protected function getLabel($field)
    {
        //return __($field);
        switch ($field) {
            case 'cc_numlast4':
                return __('Card Number');
            case 'cc_type':
                return __('Card Type');
            case 'transaction_id':
                return __('Transaction ID');
            case 'avs_response_code':
                return __('AVS Response');
            case 'cvd_response_code':
                return __('CVV Response');
            default:
                return parent::getLabel($field);
        }
    }
    
    /**
     * Returns value view
     *
     * @param string $field
     * @param string $value
     * @return string | Phrase
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    protected function getValueView($field, $value)
    {
        if ($field == 'cc_type') {
            $_objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            $helper = $_objectManager->create('Rootways\Authorizecim\Helper\Data');
            if ($helper->getCcTypeNameByCode($value)) {
                $value = $helper->getCcTypeNameByCode($value);
            }
        }
        if ($field == 'avs_response_code') {
            if (isset($this->_avsCode[trim($value)])) {
                $value = $this->_avsCode[trim($value)];
            }
        }
        if ($field == 'cvd_response_code') {
            if (is_string($value)) {
                if (isset($this->_cvvCode[trim($value)])) {
                    $value = $this->_cvvCode[trim($value)];
                }
            } else {
                $value = '';
            }
        }
        return $value;
    }
}
