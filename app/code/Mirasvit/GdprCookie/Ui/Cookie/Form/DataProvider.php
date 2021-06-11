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



namespace Mirasvit\GdprCookie\Ui\Cookie\Form;

use Magento\Ui\DataProvider\AbstractDataProvider;
use Mirasvit\GdprCookie\Api\Data\CookieInterface;
use Mirasvit\GdprCookie\Repository\CookieRepository;

class DataProvider extends AbstractDataProvider
{
    private $cookieRepository;

    /**
     * DataProvider constructor.
     *
     * @param CookieRepository $cookieRepository
     * @param string           $name
     * @param string           $primaryFieldName
     * @param string           $requestFieldName
     * @param array            $meta
     * @param array            $data
     */
    public function __construct(
        CookieRepository $cookieRepository,
        $name,
        $primaryFieldName,
        $requestFieldName,
        array $meta = [],
        array $data = []
    ) {
        $this->cookieRepository = $cookieRepository;
        $this->collection       = $this->cookieRepository->getCollection();

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
                CookieInterface::ID          => $item->getId(),
                CookieInterface::NAME        => $item->getName(),
                CookieInterface::CODE        => $item->getCode(),
                CookieInterface::IS_ACTIVE   => $item->isActive(),
                CookieInterface::DESCRIPTION => $item->getDescription(),
                CookieInterface::LIFETIME    => $item->getLifetime(),
                CookieInterface::GROUP_ID    => $item->getGroupId(),
                CookieInterface::STORE_IDS   => $item->getStoreIds(),
            ];
        }

        return $result;
    }
}
