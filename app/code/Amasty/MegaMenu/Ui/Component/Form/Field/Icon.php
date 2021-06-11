<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Ui\Component\Form\Field;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Magento\Catalog\Model\Category\FileInfo;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Form\Field;

class Icon extends Field
{
    /**
     * @var FileInfo
     */
    private $fileInfo;

    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        FileInfo $fileInfo,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->fileInfo = $fileInfo;
    }

    public function prepareDataSource(array $dataSource): array
    {
        if (isset($dataSource['data'][ItemInterface::ICON]) && is_string($dataSource['data'][ItemInterface::ICON])) {
            $filePath = $dataSource['data'][ItemInterface::ICON];
            $dataSource['data'][ItemInterface::ICON] = [$this->getImageData($filePath)];
        }

        return parent::prepareDataSource($dataSource);
    }

    private function getImageData(string $filePath): array
    {
        if ($this->fileInfo->isExist($filePath)) {
            $stat = $this->fileInfo->getStat($filePath);
            $mime = $this->fileInfo->getMimeType($filePath);
            $fileName = explode('/', $filePath);
            $fileName = array_pop($fileName);

            $imageData = [
                'name' => $fileName,
                'url' => $filePath,
                'size' => $stat['size'] ?? 0,
                'type' => $mime
            ];
        }

        return $imageData ?? [];
    }
}
