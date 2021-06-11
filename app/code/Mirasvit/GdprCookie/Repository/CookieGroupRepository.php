<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\GdprCookie\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Model\CookieGroup;
use Mirasvit\GdprCookie\Model\CookieGroupFactory;
use Mirasvit\GdprCookie\Model\ResourceModel\CookieGroup\Collection;
use Mirasvit\GdprCookie\Model\ResourceModel\CookieGroup\CollectionFactory;

class CookieGroupRepository
{
    private $entityManager;

    private $collectionFactory;

    private $cookieGroupFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        CookieGroupFactory $factory
    ) {
        $this->entityManager      = $entityManager;
        $this->collectionFactory  = $collectionFactory;
        $this->cookieGroupFactory = $factory;
    }

    /**
     * @return CookieGroupInterface[]|Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param int $id
     *
     * @return CookieGroupInterface|false
     */
    public function get($id)
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        if (!$model->getId()) {
            return false;
        }

        return $model;
    }

    /**
     * @return CookieGroup
     */
    public function create()
    {
        return $this->cookieGroupFactory->create();
    }

    /**
     * @param CookieGroupInterface $model
     *
     * @return CookieGroupInterface
     */
    public function save(CookieGroupInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param CookieGroupInterface $model
     */
    public function delete(CookieGroupInterface $model)
    {
        $this->entityManager->delete($model);
    }
}
