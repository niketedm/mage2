<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types = 1);

namespace Amasty\MegaMenu\Observer\Category;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\ItemRepositoryInterface;
use Amasty\MegaMenu\Model\Menu\ItemFactory;
use Magento\Catalog\Model\ImageUploader;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Store\Model\StoreManagerInterface;

class ContentSave implements ObserverInterface
{
    const AVAILABLE_TYPES = [
        'jpg',
        'jpeg',
        'png'
    ];

    /**
     * @var ItemFactory
     */
    private $itemFactory;

    /**
     * @var ItemRepositoryInterface
     */
    private $itemRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var ImageUploader
     */
    private $imageUploader;

    public function __construct(
        ItemFactory $itemFactory,
        ItemRepositoryInterface $itemRepository,
        RequestInterface $request,
        StoreManagerInterface $storeManager,
        ImageUploader $imageUploader = null
    ) {
        $this->itemFactory = $itemFactory;
        $this->itemRepository = $itemRepository;
        $this->request = $request;
        $this->storeManager = $storeManager;
        $this->imageUploader = $imageUploader;
    }

    public function execute(Observer $observer): void
    {
        $entity = $observer->getEvent()->getEntity();
        if ($entity instanceof AbstractModel) {
            $storeId = $this->request->getParam('store_id', $entity->getStoreId());
            $itemContent = $this->itemRepository->getByEntityId($entity->getId(), $storeId, 'category');
            if (!$itemContent) {
                $itemContent = $this->itemFactory->create([
                    'data' => [
                        'store_id' => $storeId,
                        'type' => 'category',
                        'entity_id' => $entity->getId()
                    ]
                ]);
            }

            foreach (ItemInterface::FIELDS_BY_STORE_CATEGORY as $fieldSet) {
                foreach ($fieldSet as $field) {
                    $value = $field === ItemInterface::ICON
                        ? $this->getImagePath($entity->getData($field))
                        : $entity->getData($field);
                    $itemContent->setData($field, $value);
                }
            }
            $itemContent->setName($entity->getName());
            $itemContent->setStatus($entity->getIsActive() && $entity->getIncludeInMenu());

            $this->itemRepository->save($itemContent);
        }
    }

    /**
     * @param array|string|null $imageData
     * @return string
     * @throws LocalizedException
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    private function getImagePath($imageData): string
    {
        if ($this->isTmpFileAvailable($imageData) && ($imageName = $this->getUploadedImageName($imageData))) {
            $baseMediaDir = $this->storeManager->getStore()->getBaseMediaDir();
            $newImgRelativePath = $this->imageUploader->moveFileFromTmp($imageName, true);
            $imageData = '/' . $baseMediaDir . '/' . $newImgRelativePath;
        } elseif (is_array($imageData)) {
            $imageData = $imageData[0]['url'] ?? '';
        }

        if ($imageData && !$this->isAvailableType($imageData ?? '')) {
            throw new LocalizedException(__('We don\'t recognize or support the extension type of a file uploaded to
            Menu Icon setting. Please make sure you are using the allowed format: .jpg or .png'));
        }

        return $imageData ?? '';
    }

    private function isTmpFileAvailable($value): bool
    {
        return is_array($value) && isset($value[0]['tmp_name']);
    }

    private function getUploadedImageName($value): string
    {
        if (is_array($value) && isset($value[0]['name'])) {
            return $value[0]['name'];
        }

        return '';
    }

    private function isAvailableType(string $image): bool
    {
        $type = explode('.', $image);

        return in_array(end($type), self::AVAILABLE_TYPES);
    }
}
