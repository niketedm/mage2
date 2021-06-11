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



namespace Mirasvit\GdprConsent\Repository;

use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\GdprConsent\Api\Data\ConsentInterface;
use Mirasvit\GdprConsent\Api\Data\ConsentInterfaceFactory;
use Mirasvit\GdprConsent\Model\ResourceModel\Consent\CollectionFactory;

class ConsentRepository
{
    private $entityManager;

    private $collectionFactory;

    private $factory;

    public function __construct(
        EntityManager $entityManager,
        CollectionFactory $collectionFactory,
        ConsentInterfaceFactory $factory
    ) {
        $this->entityManager     = $entityManager;
        $this->collectionFactory = $collectionFactory;
        $this->factory           = $factory;
    }

    /**
     * @return ConsentInterface[]|\Mirasvit\GdprConsent\Model\ResourceModel\Consent\Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    /**
     * @return ConsentInterface
     */
    public function create()
    {
        return $this->factory->create();
    }

    /**
     * @param int $id
     *
     * @return ConsentInterface|false
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
     * @param ConsentInterface $model
     *
     * @return ConsentInterface
     */
    public function save(ConsentInterface $model)
    {
        if (!$model->getId()) {
            $this->entityManager->save($model);
        }

        return $this->entityManager->save($model);
    }

    /**
     * @param ConsentInterface $model
     */
    public function delete(ConsentInterface $model)
    {
        $this->entityManager->delete($model);
    }
}
