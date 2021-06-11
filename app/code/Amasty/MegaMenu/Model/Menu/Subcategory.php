<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Model\Menu;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Magento\Catalog\Model\Category;
use Magento\Framework\App\RequestInterface;
use Magento\Store\Model\Store;

class Subcategory
{
    const TOP_LEVEL = 2;

    /**
     * @var Int
     */
    private $storeId;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    public function __construct(
        RequestInterface $request,
        ItemRepositoryInterface $itemRepository
    ) {
        $this->storeId = (int) $request->getParam('store', Store::DEFAULT_STORE_ID);
        $this->itemRepository = $itemRepository;
    }

    public function isShowSubcategories(Category $category): bool
    {
        $itemContent = $this->itemRepository->getByEntityId(
            $category->getEntityId(),
            Store::DEFAULT_STORE_ID,
            ItemInterface::CATEGORY_TYPE
        );
        if ($itemContent) {
            $submenuType = $itemContent->getSubmenuType();
            $subcategoriesPosition = $itemContent->getSubcategoriesPosition();
        }

        if ($this->storeId !== Store::DEFAULT_STORE_ID) {
            $itemContentStore = $this->itemRepository->getByEntityId(
                $category->getEntityId(),
                $this->storeId,
                ItemInterface::CATEGORY_TYPE
            );

            if ($itemContentStore && $itemContentStore->getSubmenuType() !== null) {
                $submenuType = $itemContentStore->getSubmenuType();
            }
            if ($itemContentStore && $itemContentStore->getSubcategoriesPosition() !== null) {
                $subcategoriesPosition = $itemContentStore->getSubcategoriesPosition();
            }
        }

        if (!isset($submenuType) || !isset($subcategoriesPosition)) {
            return false;
        }

        $level = $category->getLevel();

        return $level == self::TOP_LEVEL && $submenuType == SubmenuType::WITH_CONTENT
            || $level > self::TOP_LEVEL && $subcategoriesPosition != SubcategoriesPosition::NOT_SHOW;
    }
}
