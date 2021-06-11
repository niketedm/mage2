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
use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Model\Cookie;
use Mirasvit\GdprCookie\Model\CookieFactory;
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie\Collection;
use Mirasvit\GdprCookie\Model\ResourceModel\Cookie\CollectionFactory;

class CookieRepository
{
    private $entityManager;

    private $collectionFactory;

    private $cookieFactory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        CookieFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->cookieFactory     = $factory;
    }

    /**
     * @return CookieInterface[]|Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @param int $id
     *
     * @return CookieInterface|false
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
     * @return Cookie
     */
    public function create()
    {
        return $this->cookieFactory->create();
    }

    /**
     * @param CookieInterface $model
     *
     * @return CookieInterface
     */
    public function save(CookieInterface $model)
    {
        return $this->entityManager->save($model);
    }

    /**
     * @param CookieInterface $model
     */
    public function delete(CookieInterface $model)
    {
        $this->entityManager->delete($model);
    }
}
