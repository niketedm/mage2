<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */

namespace Magefan\BlogPlus\Model\ResourceModel;

/**
 * Class CustomerGroupFilter
 * @package Magefan\BlogPlus\Model\ResourceModel
 */
class CustomerGroupFilter
{
    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * CustomerGroupFilter constructor.
     * @param \Magento\Customer\Model\Session $customerSession
     */
    public function __construct(
        \Magento\Customer\Model\Session $customerSession
    ) {
        $this->customerSession = $customerSession;
    }

    /**
     * @param $object
     * @return mixed
     */
    public function addGroupFilter($object)
    {
        if (!$object->getFlag('group_filter_added')) {

            $customerGroups = [$this->customerSession->getCustomerGroupId()];
            if (!in_array(0, $customerGroups)) {
                $customerGroups[] = 0;
            }

            if ($object instanceof \Magefan\Blog\Model\ResourceModel\Category\Collection) {
                $object->getSelect()->join(
                    ['group_table' => $object->getTable('magefan_blog_category_group')],
                    'main_table.category_id = group_table.category_id',
                    []
                )->where('group_table.group_id IN (?)', $customerGroups);
            }
            if ($object instanceof \Magefan\Blog\Model\ResourceModel\Post\Collection) {
                $object->getSelect()->join(
                    ['group_table' => $object->getTable('magefan_blog_post_group')],
                    'main_table.post_id = group_table.post_id',
                    []
                )->where('group_table.group_id IN (?)', $customerGroups);
            }
        }
        return $object;
    }

    /**]
     * @param $object
     */
    public function loadGroupFilter($object)
    {
        $fields = '';

        if ($object instanceof \Magefan\Blog\Model\Post) {
            $fields = 'post';
        }

        if ($object instanceof \Magefan\Blog\Model\Category) {
            $fields = 'category';
        }

        if ($fields) {
            $adapter = $object->getResource()->getConnection();
            $tableName = $object->getResource()->getTable('magefan_blog_' . $fields . '_group');
            $select = $adapter->select()->from(
                $tableName,
                'group_id'
            )->where(
                $fields.'_id = ?',
                (int)$object->getId()
            );
            $groupId = $adapter->fetchCol($select);

            if (empty($groupId)) {
                $groupId = [(string)0];
            }
            $object->setGroups($groupId);
        }
    }

    /**
     * @param $object
     */
    public function saveGroupFilter($object)
    {

        $fields = '';

        if ($object instanceof \Magefan\Blog\Model\Post) {
            $fields = 'post';
        }

        if ($object instanceof \Magefan\Blog\Model\Category) {
            $fields = 'category';
        }

        if ($fields) {
            $tableName = $object->getResource()->getTable('magefan_blog_' . $fields . '_group');

            $adapter = $object->getResource()->getConnection();
            $select = $adapter->select()->from(
                $tableName,
                'group_id'
            )->where(
                $fields . '_id = ?',
                (int)$object->getId()
            );

            $oldIds = $adapter->fetchCol($select);
            $newIds = $object->getGroups();
            $newIds = is_array($newIds) ? $newIds : [0];
            if (in_array(0, $newIds)) {
                $newIds = [0];
            }
            if ($oldIds) {
                $where = [$fields . '_id = ?' => (int)$object->getId(), 'group_id IN (?)' => $oldIds];
                $adapter->delete($tableName, $where);
            }

            $rowData = [];
            if ($newIds) {
                $data = [];
                foreach ($newIds as $id) {
                    $id = (int)$id;
                    $data[] = array_merge(
                        [$fields . '_id' => (int)$object->getId(), 'group_id' => $id],
                        (isset($rowData[$id]) && is_array($rowData[$id])) ? $rowData[$id] : []
                    );
                }
                $adapter->insertMultiple($tableName, $data);
            }
        }
    }


    /**
     * @param $object
     */
    public function deleteGroupFilter($object)
    {

        $fields = '';

        if ($object instanceof \Magefan\Blog\Model\Post) {
            $fields = 'post';
        }

        if ($object instanceof \Magefan\Blog\Model\Category) {
            $fields = 'category';
        }

        if ($fields) {
            $adapter = $object->getResource();
            $condition = [$fields . '_id = ?' => (int)$object->getId()];
            $adapter->getConnection()->delete($adapter->getTable('magefan_blog_'. $fields .'_group'), $condition);
        }
    }

    /**
     * @param $object
     * @return bool
     */
    public function isVisibleForGroup($object)
    {
        $customerGroups = [$this->customerSession->getCustomerGroupId()];

        if (!in_array(0, $customerGroups)) {
            $customerGroups[] = 0;
        }

        $objectGroups = $object->getGroups();
        if (empty($objectGroups)) {
            $objectGroups = [0];
        }

        return (bool) count(array_intersect($customerGroups, $objectGroups));
    }
}
