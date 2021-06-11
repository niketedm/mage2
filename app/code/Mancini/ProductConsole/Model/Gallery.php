<?php
namespace Mancini\ProductConsole\Model;

use Exception;
use Magento\Catalog\Api\ProductAttributeMediaGalleryManagementInterface;
use Magento\Catalog\Api\ProductRepositoryInterface;
use Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter;
use Magento\Catalog\Model\Product\Gallery\EntryFactory;
use Magento\Framework\Api\Data\ImageContentInterface;
use Magento\Framework\Api\Data\ImageContentInterfaceFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;

class Gallery
{
    protected $baseImagePath = 'https://web.sleepworld.com/images/';
    protected $directoryWrite;
    protected $filesystem;
    protected $galleryEntryFactory;
    protected $galleryManagement;
    protected $productRepository;
    protected $imageContentFactory;
    protected $storeManager;
    protected $logWriter;
    protected $date = '';

    public function __construct(
        Filesystem $filesystem,
        ProductRepositoryInterface $productRepository,
        EntryFactory $galleryEntryFactory,
        ImageContentInterfaceFactory $imageContentFactory,
        ProductAttributeMediaGalleryManagementInterface $galleryManagement,
        StoreManagerInterface $storeManager,
        LogWriter $logWriter
    )
    {
        $this->directoryWrite = $filesystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $this->filesystem = $filesystem;
        $this->galleryManagement = $galleryManagement;
        $this->galleryEntryFactory = $galleryEntryFactory;
        $this->imageContentFactory = $imageContentFactory;
        $this->productRepository = $productRepository;
        $this->logWriter = $logWriter;
        $this->storeManager = $storeManager;
        $this->storeManager->setCurrentStore('admin');
    }

    public function setDate($dateString)
    {
        $this->date = $dateString;
        $this->logWriter->setSuffix($this->date);
    }

    public function downloadImage($skuSerial)
    {
        $imageUrl = $this->getImageUrl($skuSerial);
        $imagePath = 'images/' . $this->getImagePath($skuSerial);
        $content = file_get_contents($imageUrl);
        $this->directoryWrite->writeFile($imagePath, $content);

    }

    public function getImageUrl($skuSerial)
    {
        return $this->baseImagePath . $skuSerial . '/' . $skuSerial . '.jpg';
    }

    public function getImagePath($skuSerial)
    {
        return $skuSerial . '/' . $skuSerial . '.jpg';
    }

    public function refreshImage($sku, $productData)
    {
        if (!$this->isProductExist($sku)) {
            throw new Exception('SKU: ' . $sku . ' does not exist.');
        }
        if (!isset($productData['sku_serial'])) {
            throw new Exception('This product\'s SKU Serial is empty.');
        }


        $galleries = $this->prepareGalleries($productData);
        if (empty($galleries)) {
            throw new Exception('Can not get product images from image site.');
        }
        $this->removeImages($sku);
        $errors = [];
        foreach ($galleries as $galleryEntry) {
            //$galleryEntry = $this->prepareGalleryData($productData);
            try {
                $this->galleryManagement->create($sku, $galleryEntry);
            } catch (Exception $e) {
                throw new Exception($e->getMessage());
            }
        }
    }

    public function isProductExist($sku)
    {
        try {
            $product = $this->productRepository->get($sku);
            return true;
        } catch (Exception $e) {
            return false;
        }

        if ($product->getId()) {
            return true;
        } else {
            return false;
        }
    }

    public function prepareGalleries($productData)
    {

        $imageUrls = $this->getRemoteImageUrls($productData['sku_serial']);
        if (empty($imageUrls) || $imageUrls == null) {
            $this->logWriter->writeLog('Can not find images. sku: ' . $productData['sku'] . ' skuserial: ' . $productData['sku_serial'] . " Status: " . $productData['sw_status']);
            return null;
        }
        $i = 0;
        $galleries = array();
        foreach ($imageUrls as $imageUrl) {

            try {
                $imageContent = file_get_contents($imageUrl);
            } catch (Exception $e) {
                continue;
            }

            if (!$imageContent) {
                continue;
            }

            try {
                $info = pathinfo($imageUrl);
                $imageType = exif_imagetype($imageUrl);
                $mimeType = image_type_to_mime_type($imageType);
                $extension = image_type_to_extension($imageType);
            } catch (Exception $e) {
                continue;
                //$imageType = exif_imagetype($imageUrl);
                $mimeType = 'image/jpeg';//image_type_to_mime_type($imageType);
                $extension = '.' . $info['extension'];

            }
            $specialCharacters = str_split('^\/?*:";<>()|{}', 1);
            $name = str_replace($specialCharacters, '', $info['filename']);
            if (stripos($productData['sku'], "-CONFIG") !== false) {
                $name .= '-config';
            }
            //var_dump($name);
            //var_dump($productData['sku']);
            /*$contentArray = [
                \Magento\Framework\Api\Data\ImageContentInterface::BASE64_ENCODED_DATA => base64_encode($imageContent),
                \Magento\Framework\Api\Data\ImageContentInterface::TYPE => $mimeType,
                \Magento\Framework\Api\Data\ImageContentInterface::NAME => $productData['sku_serial'].$extension
            ];*/
            $content = $this->imageContentFactory->create();
            $content->setName($name . $extension)
                ->setType($mimeType)
                ->setBase64EncodedData(base64_encode($imageContent));
            $types = [];
            if ($i == 0) {
                $types = ['image', 'thumbnail', 'small_image'];
            }
            $galleryEntry = $this->galleryEntryFactory->create();
            $galleryEntry->setMediaType(ImageEntryConverter::MEDIA_TYPE_CODE)
                ->setTypes($types)
                ->setDisabled(false)
                ->setContent($content);
            $galleries[] = $galleryEntry;
            $i++;
        }

        return $galleries;
    }

    public function getRemoteImageUrls($skuSerial)
    {
        if (empty($skuSerial)) {
            return null;
        }
        $baseImageUrl = 'https://web.sleepworld.com/images/';
        $imageDir = $baseImageUrl . $skuSerial . '/';
        try {
            $dirContent = file_get_contents($imageDir);
        } catch (Exception $e) {
            return null;
        }

        if (!$dirContent) {
            return null;
        }

        preg_match_all("/HREF=\"\/images\/" . $skuSerial . "\/([\S]+)\"/", $dirContent, $files);
        if (!empty($files) && isset($files[1])) {
            $imageUrls = array();
            foreach ($files[1] as $filename) {
                $imageUrls[] = $imageDir . $filename;
            }
            return $imageUrls;
        } else {
            return null;
        }
    }

    public function removeImages($sku)
    {
        $mediaGalleryEntries = $this->galleryManagement->getList($sku);
        try {
            foreach ($mediaGalleryEntries as $entry) {
                $this->galleryManagement->remove($sku, $entry->getId());
            }
        } catch (Exception $e) {

        }


    }

    public function importImageByDir($sku, $productData)
    {
        if (!$this->isProductExist($sku)) {
            return false;
        }
        if (!isset($productData['sku_serial'])) {
            return false;
        }
        $this->removeImages($sku);
        $productData['sku'] = $sku;
        $galleries = $this->prepareGalleries($productData);
        if (empty($galleries)) {
            return false;
        }

        foreach ($galleries as $galleryEntry) {
            //$galleryEntry = $this->prepareGalleryData($productData);
            if (!$galleryEntry) {
                continue;
            }
            try {
                $this->galleryManagement->create($sku, $galleryEntry);
            } catch (Exception $e) {
                $this->logWriter->writeLog('Save Image Error:' . $sku . ' Status: ' . $productData['sw_status'] . $e->getMessage());
            }
        }

    }

    public function importImage($sku, $productData)
    {
        if (!$this->isProductExist($sku)) {
            return false;
        }
        $this->removeImages($sku);


        $galleryEntry = $this->prepareGalleryData($productData);
        if ($galleryEntry == null) {
            return false;
        }
        try {
            $this->galleryManagement->create($sku, $galleryEntry);
        } catch (Exception $e) {
            echo $e->getMessage();
        }


    }

    public function prepareGalleryData($productData)
    {
        if (empty($productData['sku_serial'])) {
            return null;
        }
        $baseImageUrl = 'https://web.sleepworld.com/images/';
        $imageUrl = $baseImageUrl . $productData['sku_serial'] . '/' . $productData['sku_serial'] . '.jpg';
        try {
            $imageContent = file_get_contents($imageUrl);
        } catch (Exception $e) {
            return null;
        }

        if (!$imageContent) {
            return null;
        }
        $imageType = exif_imagetype($imageUrl);
        $mimeType = image_type_to_mime_type($imageType);
        $extension = image_type_to_extension($imageType);
        $contentArray = [
            ImageContentInterface::BASE64_ENCODED_DATA => base64_encode($imageContent),
            ImageContentInterface::TYPE => $mimeType,
            ImageContentInterface::NAME => $productData['sku_serial'] . $extension
        ];
        $content = $this->imageContentFactory->create();
        $content->setName($productData['sku_serial'] . $extension)
            ->setType($mimeType)
            ->setBase64EncodedData(base64_encode($imageContent));
        $galleryEntry = $this->galleryEntryFactory->create();
        $galleryEntry->setMediaType(ImageEntryConverter::MEDIA_TYPE_CODE)
            ->setTypes(['image', 'thumbnail', 'small_image'])
            ->setDisabled(false)
            ->setContent($content);
        /*$galleryEntry = [
            'media_type' => \Magento\Catalog\Model\Product\Attribute\Backend\Media\ImageEntryConverter::MEDIA_TYPE_CODE,
            'label' => '',
            'position' => 1,
            'types' => ['image', 'small_image', 'thumbnail'],
            'disabled' => false,
            'content' => $contentArray
        ];*/

        return $galleryEntry;
    }

    public function checkMissingImage($sku, $productData)
    {
        if (empty($productData['sku_serial'])) {
            return false;
        }
        $baseImageUrl = 'https://web.sleepworld.com/images/';
        $imageUrl = $baseImageUrl . $productData['sku_serial'] . '/' . $productData['sku_serial'] . '.jpg';
        try {
            $imageContent = file_get_contents($imageUrl);
        } catch (Exception $e) {
            return false;
        }

        if (!$imageContent) {
            return false;
        }

        return true;
    }
}

?>
