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



namespace Mirasvit\Gdpr\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Gdpr\Api\Data\RequestInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterfaceFactory;
use Mirasvit\Gdpr\Model\ResourceModel\Request\CollectionFactory;

class RequestRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        RequestInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return RequestInterface[]|\Mirasvit\Gdpr\Model\ResourceModel\Request\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return RequestInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return RequestInterface|false
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
     * @param RequestInterface $model
     *
     * @return RequestInterface
     */
    public function save(RequestInterface $model)
    {
        if (!$model->getId()) {
            $this->entityManager->save($model);
        }

        return $this->entityManager->save($model);
    }

    /**
     * @param RequestInterface $model
     */
    public function delete(RequestInterface $model)
    {
        $this->entityManager->delete($model);
    }
}
