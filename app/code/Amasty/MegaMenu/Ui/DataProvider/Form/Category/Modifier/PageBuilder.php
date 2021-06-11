<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Ui\DataProvider\Form\Category\Modifier;

use Amasty\MegaMenu\Model\Menu\Content\Resolver;
use Magento\Ui\DataProvider\Modifier\ModifierInterface;

class PageBuilder implements ModifierInterface
{
    /**
     * @var \Magento\Framework\Module\Manager
     */
    private $moduleManager;

    public function __construct(
        \Magento\Framework\Module\Manager $moduleManager
    ) {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @inheritdoc
     */
    public function modifyData(array $data)
    {
        return $data;
    }

    /**
     * @inheritdoc
     */
    public function modifyMeta(array $meta)
    {
        return $this->modifyPageBuilder($meta);
    }

    private function modifyPageBuilder(array $meta): array
    {
        $config = &$meta['am_mega_menu_fieldset']['children']['content']['arguments']['data']['config'];
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            if ($this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')) {
                $config['default'] = Resolver::CHILD_CATEGORIES_PAGE_BUILDER;
                $config['notice'] = __('You can use the menu item Add Content for showing child categories.');
                $config['defaultNotice'] = $config['notice'];
            } else {
                $config['default'] = Resolver::CHILD_CATEGORIES;
            }
            $config['component'] = 'Amasty_MegaMenu/js/form/components/wysiwyg';
        } else {
            $config['default'] = Resolver::CHILD_CATEGORIES;
            $config['component'] = 'Amasty_MegaMenu/js/form/element/wysiwyg';
            $config['notice'] = __(
                'You can use the variable: {{child_categories_content}} for showing child categories.'
            );
            $config['defaultNotice'] = $config['notice'];
        }

        return $meta;
    }
}
