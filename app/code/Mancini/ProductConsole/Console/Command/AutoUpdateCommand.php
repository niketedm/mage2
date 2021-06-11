<?php

namespace Mancini\ProductConsole\Console\Command;

use Exception;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\Product\Attribute\Source\Status;
use Magento\Catalog\Model\Product\Visibility;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Magento\Framework\Exception\LocalizedException;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for displaying information related to indexers.
 */
class AutoUpdateCommand extends AbstractCommand
{

    protected $relatedProducts = [];
    protected $configurableProducts = [];
    protected $productCategories = [];
    protected $skuSerial = [];

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('product_console:auto_update')
            ->setDescription('Auto Update Product Data')
            ->setDefinition([
                new InputArgument(
                    'type',
                    InputArgument::OPTIONAL,
                    'Type'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     * @throws LocalizedException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$filename = $this->getImportFileName($input);
        $type = $this->getType($input);

        if ($type == 'all_images') {
            $productModel = $this->getObjectManager()->create('Magento\Catalog\Model\Product');
            $galleryModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Gallery');
            $collection = $productModel->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', Status::STATUS_ENABLED);
            foreach ($collection as $item) {
                /** @var Product $item */
                echo $item->getSku() . "\n";
                $galleryModel->importImageByDir($item->getSku(), $item->getData());
            }
        } elseif ($type == 'import_missing_images') {
            $productModel = $this->getObjectManager()->create('Magento\Catalog\Model\Product');
            $galleryModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Gallery');
            /** @var Collection $collection */
            $collection = $productModel->getCollection()
                ->addAttributeToSelect('*')
                ->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $collection->getSelect()
                ->joinLeft(
                    ['image_varchar' => 'catalog_product_entity_varchar'],
                    'e.entity_id = image_varchar.entity_id and image_varchar.attribute_id = 87',
                    ['image_varchar.value as image']
                );
            $collection->getSelect()->where('image_varchar.value = "no_selection" or image_varchar.value = "" or image_varchar.value is NULL');
            echo $collection->getSize() . "\n";
            foreach ($collection as $item) {
                /** @var Product $item */
                echo $item->getSku() . "\n";
                try {
                    if (stripos($item->getSku(), "-CONFIG") !== false) {
                        $galleryModel->importImageByDir($item->getSku(), $item->getData());
                    }
                } catch (Exception $e) {
                    echo $e->getMessage() . "\n";
                }

            }
        } elseif ($type == "missing_categories") {
            /** @var Collection $productCollection */
            $productCollection = $this->getObjectManager()->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
            $collection = $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', Status::STATUS_ENABLED);
            $collection->getSelect()
                ->joinLeft(
                    ['category_product' => 'catalog_category_product'],
                    'e.entity_id = category_product.product_id',
                    ['category_product.category_id as product_category_id']
                );
            $collection->getSelect()->where('category_product.category_id is NULL');
            if ($collection->getSize()) {
                $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
                foreach ($collection as $item) {
                    /** @var Product $item */
                    $categories = $productModel->prepareCategories($item->getData());
                    try {
                        $output->writeln($item->getSku());
                        $productModel->assignProductCategories($item->getSku(), $categories);
                    } catch (Exception $e) {
                        $output->writeln($item->getSku() . ' ' . $e->getMessage());
                    }
                }
            }
        } elseif ($type == 'featured') {
            /** @var Collection $productCollection */
            $productCollection = $this->getObjectManager()->create('Magento\Catalog\Model\ResourceModel\Product\Collection');
            $collection = $productCollection->addAttributeToSelect('*')
                ->addAttributeToFilter('visibility', Visibility::VISIBILITY_BOTH)
                ->addAttributeToFilter('status', Status::STATUS_ENABLED)
                ->addAttributeToFilter('web_collections', array('like' => '%Featured%'));
            if ($collection->getSize()) {
                $productModel = $this->getObjectManager()->create('Mancini\ProductConsole\Model\Product');
                foreach ($collection as $item) {
                    /** @var Product $item */
                    $data = $item->getData();
                    $data['brand'] = $item->getAttributeText('brand');
                    $data['brand_collection'] = $item->getAttributeText('brand_collection');
                    $categories = $productModel->prepareCategories($data);
                    try {
                        $output->writeln($item->getSku());
                        $productModel->assignProductCategories($item->getSku(), $categories);
                    } catch (Exception $e) {
                        $output->writeln($item->getSku() . ' ' . $e->getMessage());
                    }
                }
            }
        }
    }

    /**
     * Return import filename
     *
     * @return string $filename
     */
    protected function getType(InputInterface $input)
    {
        $type = '';
        if ($input->getArgument('type')) {
            $type = $input->getArgument('type');
        }
        return $type;
    }

    protected function createSourceCsvModel($file)
    {
        return $this->getObjectManager()->create('Mancini\ProductConsole\Model\Source\CsvFactory');
    }
}
