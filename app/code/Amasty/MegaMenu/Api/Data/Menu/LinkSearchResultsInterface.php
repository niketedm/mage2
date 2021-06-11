<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Api\Data\Menu;

interface LinkSearchResultsInterface extends \Magento\Framework\Api\SearchResultsInterface
{
    /**
     * @return \Amasty\MegaMenu\Api\Data\Menu\LinkInterface[]
     */
    public function getItems();

    /**
     * @param \Amasty\MegaMenu\Api\Data\Menu\LinkInterface[] $items
     * @return void
     */
    public function setItems(array $items);
}
