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



namespace Mirasvit\GdprCookie\Ui\CookieGroup\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\GdprCookie\Api\Data\CookieGroupInterface;
use Mirasvit\GdprCookie\Repository\CookieGroupRepository;

class DataProvider extends AbstractDataProvider
{
    private $cookieGroupRepository;

    /**
     * DataProvider constructor.
     *
     * @param CookieGroupRepository $cookieGroupRepository
     * @param string                $name
     * @param string                $primaryFieldName
     * @param string                $requestFieldName
     * @param array                 $meta
     * @param array                 $data
     */
    public function __construct(
        CookieGroupRepository $cookieGroupRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->cookieGroupRepository = $cookieGroupRepository;
        $this->collection            = $this->cookieGroupRepository->getCollection();

        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getData()
    {
        $result = [];

        foreach ($this->collection as $item) {
            $result[$item->getId()] = [
                CookieGroupInterface::ID          => $item->getId(),
                CookieGroupInterface::NAME        => $item->getName(),
                CookieGroupInterface::PRIORITY    => $item->getPriority(),
                CookieGroupInterface::IS_ACTIVE   => $item->isActive(),
                CookieGroupInterface::DESCRIPTION => $item->getDescription(),
                CookieGroupInterface::IS_REQUIRED => $item->isRequired(),
                CookieGroupInterface::STORE_IDS   => $item->getStoreIds(),
            ];
        }

        return $result;
    }
}
