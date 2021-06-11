<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-agreement
 */
namespace WebPanda\PeopleWatching\Model;

/**
 * Class View
 * @package WebPanda\PeopleWatching\Model
 */
class View extends \Magento\Framework\Model\AbstractModel
{
    protected function _construct()
    {
        $this->_init('WebPanda\PeopleWatching\Model\ResourceModel\View');
    }
}
