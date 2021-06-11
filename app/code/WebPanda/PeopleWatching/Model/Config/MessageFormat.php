<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Model\Config;

use Magento\Eav\Model\AttributeRepository;

/**
 * Class MessageFormat
 * @package WebPanda\PeopleWatching\Model\Config
 */
class MessageFormat extends \Magento\Framework\App\Config\Value
{
    /**
     * Validate a message field value
     *
     * @return void
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function beforeSave()
    {
        $value = $this->getValue();
        $check = preg_match_all('/{{views}}/', $value, $matches);
        if (!$check) {
            throw new \Magento\Framework\Exception\LocalizedException(__('Message must contain "{{views}}"!'));
        }
    }
}
