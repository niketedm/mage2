<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Model\Menu\Content;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Block\Html\Tree;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\OptionSource\SubmenuType;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\Module\Manager;
use Magento\Framework\View\LayoutInterface;
use Magento\Widget\Model\Template\Filter;

class Resolver
{
    const CHILD_CATEGORIES = '{{child_categories_content}}';

    // @codingStandardsIgnoreLine
    const CHILD_CATEGORIES_PAGE_BUILDER = '<div data-content-type="row" data-appearance="contained" data-element="main"><div data-enable-parallax="0" data-parallax-speed="0.5" data-background-images="{}" data-element="inner" style="justify-content: flex-start; display: flex; flex-direction: column; background-position: left top; background-size: cover; background-repeat: no-repeat; background-attachment: scroll; border-style: none; border-width: 1px; border-radius: 0px; margin: 0px 0px 10px; padding: 10px;"><div data-content-type="ammega_menu_widget" data-appearance="default" data-element="main" style="border-style: none; border-width: 1px; border-radius: 0px; margin: 0px; padding: 0px;">{{child_categories_content}}</div></div></div>';

    const CURRENT_CATEGORY_CLASS = 'current';

    /**
     * @var array
     */
    private $directivePatterns = [
        'construct' =>'/{{([a-z]{0,10})(.*?)}}/si'
    ];

    /**
     * @var array
     */
    private $categoriesHtml = [];

    /**
     * @var Filter
     */
    private $filter;

    /**
     * @var Manager
     */
    private $moduleManager;

    /**
     * @var LayoutInterface
     */
    private $layout;

    public function __construct(
        Filter $filter,
        Manager $moduleManager,
        LayoutInterface $layout,
        $directivePatterns = []
    ) {
        $this->filter = $filter;
        $this->moduleManager = $moduleManager;
        $this->layout = $layout;
        $this->directivePatterns = array_merge($this->directivePatterns, $directivePatterns);
    }

    public function resolve(Node $node): ?string
    {
        $content = $node->getData(ItemInterface::CONTENT);
        if ($node->getIsCategory() && $content === null) {
            $content = $this->getDefaultContent();
        }

        $content = $this->parseContent($node, $content);

        return $content;
    }

    public function resolveCategoriesContent(Node $node): string
    {
        $id = $node->getId();
        if (!isset($this->categoriesHtml[$id]) && $node->hasChildren()) {
            $this->categoriesHtml[$id] = sprintf(
                '<div class="ammenu-categories-container ammenu-categories">%s</div>',
                $this->getTreeHtml($node)
            );
        }

        return $this->categoriesHtml[$id] ?? '';
    }

    public function getHighLightClass(Node $node): string
    {
        return ($node->getData('has_active') || $node->getData('is_active')) ? self::CURRENT_CATEGORY_CLASS : '';
    }

    private function getTreeHtml(Node $node, int $level = 1): string
    {
        $html = '';

        if ($node->hasChildren()) {
            $block = $this->layout->createBlock(
                Tree::class,
                '',
                [
                    'data' => [
                        'node' => $node,
                        'level' => $level
                    ]
                ]
            );
            $html = $block ? $block->toHtml() : '';
        }

        return $html;
    }

    private function getDefaultContent(): string
    {
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')
            && $this->moduleManager->isEnabled('Amasty_MegaMenuPageBuilder')
        ) {
            $content = self::CHILD_CATEGORIES_PAGE_BUILDER;
        } else {
            $content = self::CHILD_CATEGORIES;
        }

        return $content;
    }

    private function parseContent(Node $node, $content): ?string
    {
        if ($content) {
            if ($content !== self::CHILD_CATEGORIES && $this->isDirectivesExists($content)) {
                $content = $this->parseWysiwyg($content);
            }

            $content = $this->isSubmenuContentEnabled($node)
                ? $this->removeVariables($content)
                : $this->parseVariables($node, $content);
        }

        return $content;
    }

    private function removeVariables(string $content): string
    {
        return str_replace(self::CHILD_CATEGORIES, '', $content);
    }

    private function parseVariables(Node $node, $content): string
    {
        preg_match_all('@\{{(.+?)\}}@', $content, $matches);

        if (isset($matches[1]) && !empty($matches[1])) {
            foreach ($matches[1] as $match) {
                $result = '';

                switch ($match) {
                    case 'child_categories_content':
                        $result = $this->resolveCategoriesContent($node);
                        break;
                }

                $content = str_replace('{{' . $match . '}}', $result, $content);
            }
        }

        return $content;
    }

    private function parseWysiwyg(string $content): string
    {
        $content = $this->filter->filter($content);

        return $content;
    }

    private function isDirectivesExists(string $html): bool
    {
        $matches = false;
        if ($this->moduleManager->isEnabled('Magento_PageBuilder')) {
            return true;
        }

        foreach ($this->directivePatterns as $pattern) {
            if (preg_match($pattern, $html)) {
                $matches = true;
                break;
            }
        }

        return $matches;
    }

    public function isSubmenuContentEnabled(Node $node): bool
    {
        $mainNode = $this->getParentNode($node, Subcategory::TOP_LEVEL);

        return  $mainNode->getData(ItemInterface::SUBMENU_TYPE) == SubmenuType::WITH_CONTENT;
    }

    private function getParentNode(Node $node, int $level): Node
    {
        if ($node->getLevel() > $level) {
            $node = $this->getParentNode($node->getParent(), $level);
        }

        return $node;
    }
}
