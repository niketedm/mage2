<?php

namespace Mancini\ProductConsole\Console\Command;

use Exception;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for displaying information related to indexers.
 */
class ClearDataCommand extends AbstractCommand
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
        $this->setName('product_console:clear_data')
            ->setDescription('Clear Product Data')
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
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$filename = $this->getImportFileName($input);
        $type = $this->getType($input);

        if ($type == 'products') {
            $productModel = $this->getObjectManager()->create('Magento\Catalog\Model\Product');
            $productCollection = $productModel->getCollection();
            echo $productCollection->getSize() . "\n";
            $productCollection->delete();
        } elseif ($type == 'options') {
            $productAttributeOptionManagement = $this->getObjectManager()->create('Magento\Catalog\Model\Product\Attribute\OptionManagement');
            $attributeCodeArray = array('brand', 'matt_firmness', 'comfort', 'matt_size', 'material', 'finish_color', 'brand_collection');
            foreach ($attributeCodeArray as $attributeCode) {
                $options = $productAttributeOptionManagement->getItems($attributeCode);
                echo $attributeCode . "\n";
                foreach ($options as $id => $option) {
                    try {
                        $productAttributeOptionManagement->delete($attributeCode, $option->getValue());
                    } catch (Exception $e) {
                        echo $attributeCode . " " . $e->getMessage() . "\n";
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
            //$requestedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        }
        return $type;
    }

    protected function createSourceCsvModel($file)
    {
        return $this->getObjectManager()->create('Mancini\ProductConsole\Model\Source\CsvFactory');
    }
}
