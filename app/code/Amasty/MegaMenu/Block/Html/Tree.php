<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Block\Html;

use Amasty\MegaMenu\Helper\Config;
use Amasty\MegaMenu\Model\Menu\Content\Matrix;
use Amasty\MegaMenu\Model\Menu\Content\Resolver;
use Magento\Framework\Data\Tree\Node;
use Magento\Framework\View\Element\Template;
use Magento\Framework\View\Element\Template\Context;

class Tree extends Template
{
    const MAX_COLUMN_COUNT = 10;

    const DEFAULT_COLUMN_COUNT = 4;

    /**
     * @var string
     */
    protected $_template = 'Amasty_MegaMenu::html/tree.phtml';

    /**
     * @var Resolver
     */
    private $contentResolver;

    /**
     * @var Matrix
     */
    private $matrixConverter;

    /**
     * @var Config
     */
    private $config;

    public function __construct(
        Context $context,
        Resolver $contentResolver,
        Matrix  $matrixConverter,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->contentResolver = $contentResolver;
        $this->matrixConverter = $matrixConverter;
        $this->config = $config;
    }

    public function getColumnCount(Node $node): int
    {
        $count = $node->getColumnCount() !== null ? (int)$node->getColumnCount() : self::DEFAULT_COLUMN_COUNT;

        return min($count, static::MAX_COLUMN_COUNT);
    }

    public function getNewColumnIndexes(int $nodesCount, int $columnCount, int $level): array
    {
        return $this->matrixConverter->getNewColumnIndexes($nodesCount, $columnCount, $level);
    }

    public function getHighLightClass(Node $node): string
    {
        return $this->contentResolver->getHighLightClass($node);
    }

    public function getChildRecursiveHtml(Node $node, int $level = 1): string
    {
        return $this->setData(['node' => $node, 'level' => $level])->toHtml();
    }

    public function isChangeColumn(int $level, int $counter, int $nodesCount, int $columnCount): bool
    {
        $newIndex = $this->getNewColumnIndexes($nodesCount, $columnCount, $level);

        return $level === 1
            && $counter !== 1
            && ($nodesCount <= $columnCount || in_array($counter - 1, $newIndex));
    }

    public function checkNodeForIcons(Node $node): bool
    {
        foreach ($node->getChildren() as $childNode) {
            if ($childNode->getIcon()) {
                return true;
            }
        }

        return false;
    }

    public function getIcon(Node $node): string
    {
        if ($node->getIcon()) {
            $url = $node->getIcon();
            $url = rtrim($this->getBaseUrl(), '/') . str_replace(' ', '%20', $url);
        }

        return $url ?? '';
    }
}
