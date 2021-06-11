<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-navigation
 * @version   1.1.0
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Plugin;

use Magento\Framework\App\Request\Http as RequestHttp;
use Magento\Framework\Registry;
use Mirasvit\LayeredNavigation\Model\Config;
use Mirasvit\LayeredNavigation\Service\VersionService;

class ImageProductSetCorrectCollorPlugin
{
    /**
     * @var VersionService
     */
    private $versionService;
    /**
     * @var RequestHttp
     */
    private $request;
    /**
     * @var Registry
     */
    private $registry;

    /**
     * ImageProductSetCorrectCollorPlugin constructor.
     * @param Registry $registry
     * @param RequestHttp $request
     * @param VersionService $versionService
     */
    public function __construct(
        Registry $registry,
        RequestHttp $request,
        VersionService $versionService
    ) {
        $this->registry       = $registry;
        $this->request        = $request;
        $this->versionService = $versionService;
    }

    /**
     * @param mixed $subject
     * @param string $file
     *
     * @return string
     */
    public function beforeSetBaseFile($subject, $file)
    {
        $productData = $this->registry->registry(Config::NAV_IMAGE_REG_PRODUCT_DATA);
        if (!$productData || !is_object($productData[ImageProductRegisterPlugin::PRODUCT])) {
            return $file;
        }

        $product           = $productData[ImageProductRegisterPlugin::PRODUCT];
        $destinationSubdir = $productData[ImageProductRegisterPlugin::ATTRIBUTE_TYPE];

        if (($colorAttribute = $this->request->getParam('color'))
            && $product->getTypeId() == \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $children = $product->getTypeInstance()->getUsedProducts($product);

            foreach ($children as $child) {
                if ($child->getData('color') == $colorAttribute
                    && ($imageFile = $child->getData($destinationSubdir))) {
                    $file = $imageFile;
                    break;
                }
            }
        }

        return (version_compare($this->versionService->getVersion(), '2.2.0') < 0) ? [$file] : $file;
    }
}
