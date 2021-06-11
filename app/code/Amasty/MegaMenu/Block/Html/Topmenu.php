<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Block\Html;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Model\Menu\Subcategory;
use Amasty\MegaMenu\Model\Menu\TreeResolver;
use Amasty\MegaMenu\Model\OptionSource\IconStatus;
use Amasty\MegaMenu\Model\OptionSource\MenuWidth;
use Amasty\MegaMenu\Model\OptionSource\Status;
use Amasty\MegaMenu\Model\OptionSource\SubcategoriesPosition;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Framework\View\Element\Template;

class Topmenu extends Template implements IdentityInterface, ArgumentInterface
{
    const HAMBURGER_TEMPLATES = [
        'Amasty_MegaMenu::html/hamburger/topmenu.phtml',
        'Amasty_MegaMenu::html/hamburger/leftmenu.phtml',
        'Amasty_MegaMenu::html/hamburger/submenu.phtml'
    ];

    const MOBILE_VIEW = 'mobile';

    const DESKTOP_VIEW = 'desktop';

    const IS_CHILD_HAS_ICON = 'isChildHasIcons';

    /**
     * @var array
     */
    private $directivePatterns = [
        'construct' =>'/{{([a-z]{0,10})(.*?)}}/si'
    ];

    /**
     * @var TreeResolver
     */
    private $treeResolver;

    /**
     * @var Node|null
     */
    private $menu = null;

    /**
     * @var \Amasty\MegaMenu\Helper\Config
     */
    private $config;

    /**
     * @var \Magento\Customer\Model\Session
     */
    private $customerSession;

    /**
     * @var \Amasty\MegaMenu\Model\Menu\Content\Resolver
     */
    private $contentResolver;

    /**
     * @var SubcategoriesPosition
     */
    private $subcategoriesPosition;

    public function __construct(
        TreeResolver $treeResolver,
        Template\Context $context,
        \Amasty\MegaMenu\Model\Menu\Content\Resolver $contentResolver,
        \Amasty\MegaMenu\Helper\Config $config,
        \Magento\Customer\Model\Session $customerSession,
        SubcategoriesPosition $subcategoriesPosition,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->treeResolver = $treeResolver;
        $this->config = $config;
        $this->customerSession = $customerSession;
        $this->contentResolver = $contentResolver;
        $this->subcategoriesPosition = $subcategoriesPosition;
    }

    /**
     * @inheritDoc
     */
    protected function _toHtml()
    {
        $html = '';

        if (!$this->isHamburgerEnabled() || in_array($this->getTemplate(), self:: HAMBURGER_TEMPLATES)) {
            $html = parent::_toHtml();
        }

        return $html;
    }

    /**
     * @return Node
     */
    public function getMenuTree()
    {
        if ($this->menu === null) {
            $this->menu = $this->treeResolver->get(
                (int) $this->_storeManager->getStore()->getId()
            );
        }

        return $this->menu;
    }

    /**
     * @return array
     */
    public function getHamburgerMainNodes()
    {
        $mainNodes = [];

        foreach ($this->getMenuTree()->getChildren() as $node) {
            $mainNodes[] = $node;
        }

        return $mainNodes;
    }

    /**
     * @return array|Node\Collection
     */
    public function getMainNodes()
    {
        if ($this->isHamburgerEnabled()) {
            $mainNodes = [];

            foreach ($this->getMenuTree()->getChildren() as $node) {
                if ($node->getData('is_category')) {
                    continue;
                }

                $mainNodes[] = $node;
            }

            return $mainNodes;
        }

        return $this->getMenuTree()->getChildren();
    }

    /**
     * @return bool
     */
    public function isHamburgerEnabled()
    {
        return $this->config->isHamburgerEnabled();
    }

    /**
     * @return string
     */
    public function getMobileTemplateClass()
    {
        return $this->config->getMobileTemplateClass();
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    public function getWidth($node)
    {
        $width = $node->getData(ItemInterface::WIDTH);

        if ($width === null) {
            $width = MenuWidth::FULL;
        }

        return $width ? 'auto' : 'full';
    }

    /**
     * @param $node
     *
     * @return float
     */
    public function getWidthValue($node)
    {
        $widthMode = $node->getData(ItemInterface::WIDTH);
        $result = 0;

        if ($widthMode == MenuWidth::CUSTOM) {
            $result = (float)$node->getData(ItemInterface::WIDTH_VALUE);
        }

        return $result;
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    public function getCategoriesHtml(Node $node)
    {
        return $this->contentResolver->resolveCategoriesContent($node);
    }

    /**
     * @return int
     */
    public function getStickyState()
    {
        return (int)$this->config->getModuleConfig('general/sticky');
    }

    /**
     * @param Node $node
     *
     * @return string
     */
    public function getContent(Node $node)
    {
        return $this->contentResolver->resolve($node);
    }

    /**
     * @return string[]
     */
    public function getIdentities()
    {
        return [\Amasty\MegaMenu\Model\Menu\Item::CACHE_TAG];
    }

    public function getCustomerFullName(): string
    {
        if ($this->customerSession->isLoggedIn()) {
            $customer = $this->customerSession->getCustomer();

            return $customer->getFirstname() . ' ' . $customer->getLastname();
        }

        return '';
    }

    /**
     * @param Node $node
     * @return string
     */
    public function getHighLightClass(Node $node)
    {
        return $this->contentResolver->getHighLightClass($node);
    }

    /**
     * @param string $id
     * @param int $status
     * @param string $view
     * @return bool
     */
    public function isNeedDisplay($id = '', $status = 0, $view = self::DESKTOP_VIEW)
    {
        if (strpos($id, 'custom-') === false) {
            return true;
        }

        $result = false;

        if ($view == self::DESKTOP_VIEW) {
            $result = ($status == Status::ENABLED || $status == Status::DESKTOP);
        }

        if ($view == self::MOBILE_VIEW) {
            $result = ($status == Status::ENABLED || $status == Status::MOBILE);
        }

        return $result;
    }

    public function getIcon(Node $node): string
    {
        $url = '';
        if ($node->getIcon()) {
            $url = $node->getIcon();
            $url = rtrim($this->getBaseUrl(), '/') . str_replace(' ', '%20', $url);
        }

        return $url;
    }

    public function isShowIcons(string $view = self::DESKTOP_VIEW): bool
    {
        return $view == self::DESKTOP_VIEW ? $this->isShowDesktopIcons() : $this->isShowMobileIcons();
    }

    private function isShowMobileIcons(): bool
    {
        return in_array($this->config->getShowIcons(), [IconStatus::ENABLED, IconStatus::MOBILE]);
    }

    private function isShowDesktopIcons(): bool
    {
        return in_array($this->config->getShowIcons(), [IconStatus::ENABLED, IconStatus::DESKTOP]);
    }

    public function isSubmenuContentEnabled(): bool
    {
        return  $this->contentResolver->isSubmenuContentEnabled($this->getData('mainNode'));
    }

    /**
     * @return string
     */
    public function getJsLayout()
    {
        $tree = $this->getNodeData($this->getData('mainNode'));
        $this->jsLayout['components']['submenu']['config']['submenuData'] = $tree;

        return json_encode($this->jsLayout, JSON_HEX_TAG);
    }

    public function isChildHasIcons(Node $node): bool
    {
        if ($node->getChildren()->count()) {
            foreach ($node->getChildren() as $child) {
                if ($child->getData(ItemInterface::ICON)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function getNodeData(Node $node): array
    {
        $data = [];
        if ($node->getChildren()->count()) {
            foreach ($node->getChildren() as $child) {
                $data[] = $this->getNodeData($child);
                if ($child->getData(ItemInterface::ICON) && !$node->getData(self::IS_CHILD_HAS_ICON)) {
                    $node->setData(self::IS_CHILD_HAS_ICON, true);
                }
            }
            $nodeData = $this->getCurrentNodeData($node, $data);
        } else {
            $nodeData = $this->getCurrentNodeData($node, []);
        }

        return $nodeData;
    }

    private function getCurrentNodeData(Node $node, array $elems = []): array
    {
        $data = [
            ItemInterface::NAME => $node->getData('name'),
            'type' => $this->getNodeType($node),
            ItemInterface::CONTENT => $this->contentResolver->resolve($node),
            'elems' => $elems,
            'url' => $node->getData('url'),
            'current' => $node->getData('has_active') || $node->getData('is_active'),
            self::IS_CHILD_HAS_ICON => (bool) $node->getData(self::IS_CHILD_HAS_ICON)
        ];

        if ($node->getData(ItemInterface::LABEL)) {
            $data[ItemInterface::LABEL] = [
                ItemInterface::LABEL => $node->getData(ItemInterface::LABEL),
                ItemInterface::LABEL_TEXT_COLOR => $node->getData(ItemInterface::LABEL_TEXT_COLOR),
                ItemInterface::LABEL_BACKGROUND_COLOR => $node->getData(ItemInterface::LABEL_BACKGROUND_COLOR)
            ];
        }
        if ($node->getData(ItemInterface::ICON)) {
            $data[ItemInterface::ICON] = $this->getIcon($node);
        }

        return $data;
    }

    private function getNodeType(Node $node): ?array
    {
        $options = $this->subcategoriesPosition->toOptionArray(true);
        $position = $node->getData(ItemInterface::SUBCATEGORIES_POSITION);
        if ($position === null) {
            $position = $this->getDefaultPosition((int) $node->getData('level'));
        }

        if (isset($options[$position]['label'])) {
            $type = $options[$position]['label']->getText();
            $type = [
              'value' => (int) $position,
              'label' => strtolower($type)
            ];
        }

        return $type ?? null;
    }

    private function getDefaultPosition(int $level): int
    {
        return $level === Subcategory::TOP_LEVEL ? SubcategoriesPosition::LEFT : SubcategoriesPosition::NOT_SHOW;
    }

    public function getTarget(Node $node): string
    {
        return $node->getLinkType() == UrlKey::EXTERNAL_URL ? '_blank' : '_self';
    }

    public function getIconStatus(): string
    {
        return $this->config->getShowIcons();
    }
}
