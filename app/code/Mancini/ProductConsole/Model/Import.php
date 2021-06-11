<?php
namespace Mancini\ProductConsole\Model;

use InvalidArgumentException;
use Magento\Framework\Api\AbstractSimpleObject;
use Magento\Framework\Api\ExtensibleDataInterface;
use Magento\Framework\DataObject;
use Magento\Framework\DataObject\Copy\Config;
use Magento\Framework\Filesystem;

class Import
{

    /**
     * @var Config
     */
    protected $fieldsetConfig;

    /**
     * @param Import\Source\CsvFactory $sourceCsvFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        Config $fieldsetConfig
    )
    {
        $this->fieldsetConfig = $fieldsetConfig;
    }

    public function getDataArray($fieldset, $aspect, $source, $root = 'global')
    {
        if (!(is_array($source) /*|| $source instanceof \Magento\Framework\DataObject*/)) {
            return null;
        }
        $fields = $this->fieldsetConfig->getFieldset($fieldset, $root);
        if ($fields === null) {
            return $source;
        }

        $data = [];
        foreach ($fields as $code => $node) {
            if (empty($node[$aspect])) {
                continue;
            }

            $value = $this->_getFieldsetFieldValue($source, $code);

            $targetCode = (string)$node[$aspect];
            $targetCode = $targetCode == '*' ? $code : $targetCode;
            $data[$targetCode] = $value;
        }

        return $data;
    }

    /**
     * Get value of source by code
     *
     * @param mixed $source
     * @param string $code
     *
     * @return mixed
     * @throws InvalidArgumentException
     */
    protected function _getFieldsetFieldValue($source, $code)
    {
        if (is_array($source)) {
            $value = isset($source[$code]) ? $source[$code] : null;
        } elseif ($source instanceof DataObject) {
            $value = $source->getDataUsingMethod($code);
        } elseif ($source instanceof ExtensibleDataInterface) {
            $value = $this->getAttributeValueFromExtensibleDataObject($source, $code);
        } elseif ($source instanceof AbstractSimpleObject) {
            $sourceArray = $source->__toArray();
            $value = isset($sourceArray[$code]) ? $sourceArray[$code] : null;
        } else {
            throw new InvalidArgumentException(
                'Source should be array, Magento Object, ExtensibleDataInterface, or AbstractSimpleObject'
            );
        }
        return $value;
    }


}

?>
