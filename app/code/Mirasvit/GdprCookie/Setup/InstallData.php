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



namespace Mirasvit\GdprCookie\Setup;

use Magento\Framework\Module\Dir;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\GdprCookie\Repository\CookieGroupRepository;
use Mirasvit\GdprCookie\Repository\CookieRepository;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    const DEFAULT_COOKIE_FILE_NAME = 'default_cookie.json';
    const DEFAULT_COOKIE_GROUP_FILE_NAME = 'default_cookie_group.json';

    private $directory;

    private $cookieRepository;

    private $cookieGroupRepository;

    public function __construct(
        Dir $directory,
        CookieRepository $cookieRepository,
        CookieGroupRepository $cookieGroupRepository
    ) {
        $this->directory             = $directory;
        $this->cookieRepository      = $cookieRepository;
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->insertCookieGroupData($setup);
        $this->insertCookieData($setup);
    }

    private function insertCookieData(ModuleDataSetupInterface $setup)
    {
        // use __DIR__ to compatibility with m2.1 and m2.2
        $cookieData = SerializeService::decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . self::DEFAULT_COOKIE_FILE_NAME));

        foreach ($cookieData as $data) {
            if (!empty($data['store_ids'])) {
                $data['store_ids'] = $this->prepareStoreIds($data['store_ids']);
            }

            $model = $this->cookieRepository->create();
            $model->setData($data);
            $this->cookieRepository->save($model);
        }

    }

    private function insertCookieGroupData(ModuleDataSetupInterface $setup)
    {
        // use __DIR__ to compatibility with m2.1 and m2.2
        $groupData = SerializeService::decode(file_get_contents(__DIR__ . DIRECTORY_SEPARATOR . self::DEFAULT_COOKIE_GROUP_FILE_NAME));

        foreach ($groupData as $data) {
            if (!empty($data['store_ids'])) {
                $data['store_ids'] = $this->prepareStoreIds($data['store_ids']);
            }

            $model = $this->cookieGroupRepository->create();
            $model->setData($data);
            $this->cookieGroupRepository->save($model);
        }
    }

    /**
     * @param array $storeIds
     *
     * @return string
     */
    private function prepareStoreIds($storeIds)
    {
        return implode(',', $storeIds);
    }
}
