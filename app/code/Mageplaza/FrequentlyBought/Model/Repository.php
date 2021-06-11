<?php
/**
 * Mageplaza
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Mageplaza.com license that is
 * available through the world-wide-web at this URL:
 * https://www.mageplaza.com/LICENSE.txt
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Mageplaza
 * @package     Mageplaza_FrequentlyBought
 * @copyright   Copyright (c) Mageplaza (https://www.mageplaza.com/)
 * @license     https://www.mageplaza.com/LICENSE.txt
 */

namespace Mageplaza\FrequentlyBought\Model;

use Exception;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Mageplaza\FrequentlyBought\Api\Data\ProductLinkInterface;
use Mageplaza\FrequentlyBought\Api\Data\ProductLinkInterfaceFactory;
use Mageplaza\FrequentlyBought\Api\FrequentlyBoughtRepositoryInterface;
use Mageplaza\FrequentlyBought\Helper\Data;
use Mageplaza\FrequentlyBought\Model\ResourceModel\FrequentlyBought as ResourceModel;
use Mageplaza\FrequentlyBought\Model\ResourceModel\FrequentlyBought\Collection;

/**
 * Class Repository
 * @package Mageplaza\FrequentlyBought\Model
 */
class Repository implements FrequentlyBoughtRepositoryInterface
{
    /**
     * @var ResourceModel
     */
    protected $linkResource;

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var ProductRepositoryInterface
     */
    protected $productRepository;

    /**
     * @var Data
     */
    protected $helperData;

    /**
     * @var ProductLinkInterfaceFactory
     */
    protected $fbtModelFactory;

    /**
     * Repository constructor.
     *
     * @param ProductRepositoryInterface $productRepository
     * @param Data $helperData
     * @param ProductLinkInterfaceFactory $fbtModelFactory
     */
    public function __construct(
        ProductRepositoryInterface $productRepository,
        Data $helperData,
        ProductLinkInterfaceFactory $fbtModelFactory
    ) {
        $this->productRepository = $productRepository;
        $this->helperData = $helperData;
        $this->fbtModelFactory = $fbtModelFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function getList($sku)
    {
        $productRepo = $this->productRepository->get($sku);
        $productId = $productRepo->getId();
        $productSku = $productRepo->getSku();
        if (!$this->getLinkResource()->hasProductLinks($productId)) {
            throw new NoSuchEntityException(
                __(
                    'Product %1 doesn\'t have frequently bought products',
                    $sku
                )
            );
        }
        /** @var FrequentlyBought $fbtModel */
        $fbtModel = $this->fbtModelFactory->create();
        /** @var Collection $collection */
        $collection = $fbtModel->getFbtCollection()->setProduct($productRepo)->addProductIdFilter();
        $output = [];
        /** @var ProductLinkInterface $item */
        foreach ($collection as $item) {
            $product = $this->productRepository->getById($item->getLinkedProductId());
            $itemId = $item->getId();
            $output[$itemId] = $item->setSku($productSku)
                ->setLinkedProductSku($product->getSku())
                ->setLinkedProductType($product->getTypeId());
            $output[$itemId]['position'] = $output[$itemId]['position'] ?? 0;
        }

        usort($output, function ($itemA, $itemB) {
            $posA = (int)$itemA['position'];
            $posB = (int)$itemB['position'];

            return $posA <=> $posB;
        });

        return $output;
    }

    /**
     * {@inheritdoc}
     */
    public function save(ProductLinkInterface $entity)
    {
        $linkedProduct = $this->productRepository->get($entity->getLinkedProductSku());
        $product = $this->productRepository->get($entity->getSku());
        $links = [];
        $data = $entity->__toArray();
        $data['product_id'] = $linkedProduct->getId();
        $links[$linkedProduct->getId()] = $data;

        try {
            $productData = $this->getMetadataPool()->getHydrator(ProductInterface::class)->extract($product);
            $this->getLinkResource()->saveProductLinks(
                $productData[$this->getMetadataPool()->getMetadata(ProductInterface::class)->getLinkField()],
                $links
            );
        } catch (Exception $e) {
            throw new CouldNotSaveException(__('The linked products data is invalid. Verify the data and try again.'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ProductLinkInterface $entity)
    {
        $linkedProduct = $this->productRepository->get($entity->getLinkedProductSku());
        $product = $this->productRepository->get($entity->getSku());
        $linkId = $this->getLinkResource()->getProductLinkId(
            $product->getId(),
            $linkedProduct->getId()
        );

        if (!$linkId) {
            throw new NoSuchEntityException(
                __(
                    'Product with SKU \'%1\' is not linked to product with SKU \'%2\'',
                    $entity->getLinkedProductSku(),
                    $entity->getSku()
                )
            );
        }

        try {
            $this->getLinkResource()->deleteProductLink($linkId);
        } catch (Exception $exception) {
            throw new CouldNotSaveException(__('The linked products data is invalid. Verify the data and try again.'));
        }

        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById($sku, $linkedProductSku)
    {
        $linkItems = $this->getList($sku);
        /** @var ProductLinkInterface $linkItem */
        foreach ($linkItems as $linkItem) {
            if ($linkItem->getLinkedProductSku() === $linkedProductSku) {
                return $this->delete($linkItem);
            }
        }
        throw new NoSuchEntityException(
            __(
                'Product %1 doesn\'t have linked %2',
                [
                    $sku,
                    $linkedProductSku
                ]
            )
        );
    }

    /**
     * @return ResourceModel
     */
    protected function getLinkResource()
    {
        if ($this->linkResource === null) {
            $this->linkResource = ObjectManager::getInstance()
                ->get(ResourceModel::class);
        }

        return $this->linkResource;
    }

    /**
     * @return MetadataPool
     */
    private function getMetadataPool()
    {
        if ($this->metadataPool === null) {
            $this->metadataPool = ObjectManager::getInstance()
                ->get(MetadataPool::class);
        }

        return $this->metadataPool;
    }
}
