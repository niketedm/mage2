<?php
/**
 * @author      WebPanda
 * @package     WebPanda_PeopleWatching
 * @copyright   Copyright (c) WebPanda (https://webpanda-solutions.com/)
 * @license     https://webpanda-solutions.com/license-a
 * greement
 */
namespace WebPanda\PeopleWatching\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

/**
 * Class View
 * @package WebPanda\PeopleWatching\Model\ResourceModel
 */
class View extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('wp_product_view', 'id');
    }

    /**
     * @param $sessionId
     * @param $productId
     */
    public function addProductView($sessionId, $productId)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();
        $saveData = [
            'session_id' => $sessionId,
            'product_id' => $productId
        ];
        $updateData = [
            'viewed_at' => new \Zend_Db_Expr('NOW()'),
        ];
        $connection->insertOnDuplicate($table, $saveData, $updateData);
    }

    /**
     * @param $lifetime
     * @param $productId
     * @param $ignoredSessionId
     * @return string
     */
    public function getViewCount($lifetime, $productId, $ignoredSessionId)
    {
        $connection = $this->getConnection();
        $table = $this->getMainTable();

        $bind = [':lifetime' => $lifetime, ':product_id' => $productId, ':ignored_session_id' => $ignoredSessionId];
        $select = $connection->select()->from(
            $table,
            ['view_count' => 'Count(*)']
        )->where(
            'viewed_at > date_sub(now(), interval :lifetime minute)'
        )->where(
            'product_id = :product_id'
        )->where(
            'session_id <> :ignored_session_id'
        );

        return $connection->fetchOne($select, $bind);
    }
}
