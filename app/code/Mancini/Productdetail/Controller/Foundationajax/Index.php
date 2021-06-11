<?php
/**
 * Copyright ©  All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

namespace Mancini\Productdetail\Controller\Foundationajax;

class Index extends \Magento\Framework\App\Action\Action
{

   /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;

    /**
     * @var Repository
     */
    protected $_productRepository;

    /**
     * @var \Magento\Framework\Pricing\Helper\Data
     */
    protected $_priceHelper;

     /**
     * @var \Magento\Catalog\Helper\Image
     */
    protected $_imageHelper;

    /**
     * @var \Magento\Framework\Registry
    */
    protected $_registry;

    /**
     * Constructor
     * @param \Magento\Catalog\Model\ProductRepository $productRepository
     * @param \Magento\Framework\App\Action\Context  $context
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Pricing\Helper\Data $priceHelper
     * @param \Magento\Catalog\Helper\Image $imageHelper
     * @param \Magento\Framework\Registry $registry    
     */
    public function __construct(
        \Magento\Catalog\Model\ProductRepository $productRepository,
        \Magento\Framework\App\Action\Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Pricing\Helper\Data $priceHelper,
        \Magento\Catalog\Helper\Image $imageHelper,
        \Magento\Framework\Registry $registry,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->resultPageFactory = $resultPageFactory;
        $this->jsonHelper = $jsonHelper;
        $this->_priceHelper = $priceHelper;
        $this->logger = $logger;
        $this->_productRepository = $productRepository;
        $this->_imageHelper       = $imageHelper;
        $this->_registry = $registry;
        parent::__construct($context);
    }

    /**
     * Execute view action
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            $html ='<option value="">No Foundation</option>';
            $foundation = 0;
            $this->_registry->register('linktype', 'related');

            $selectAttr = $this->getRequest()->getParam('selectprd');
            $product = $this->_productRepository->getById($selectAttr);
            $mainPrice = $this->_priceHelper->currency($product->getFinalPrice(), true, false);

            $fndPrd = $this->getFoundationProducts($selectAttr);
            if($fndPrd != ''){
                $html .= $fndPrd;
                $foundation = 1;
            }
            
            $result = ['success' => true,'value'=>$html,'sizeprice'=>$mainPrice, 'foundation'=>$foundation];
            return $this->jsonResponse($result);

        } catch (\Magento\Framework\Exception\LocalizedException $e) {
            return $this->jsonResponse($e->getMessage());
        } catch (\Exception $e) {
            $this->logger->critical($e);
            return $this->jsonResponse($e->getMessage());
        }
    }

    /**
     * Create json response
     *
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function jsonResponse($response = '')
    {
        return $this->getResponse()->representJson(
            $this->jsonHelper->jsonEncode($response)
        );
    }

     /**
     * Function to get the foundation products of a product
     * @param productId
     */

    public function getFoundationProducts ($productId)
    {    
        $associatePrd = '';
        $product = $this->_productRepository->getById($productId);
        $relatedProducts = $product->getRelatedProducts();

        if (!empty($relatedProducts)) {
            foreach ($relatedProducts as $relatedProduct) {
                $relatedPrd = $this->_productRepository->getById($relatedProduct->getId());
                $formattedPrice = $this->_priceHelper->currency($relatedPrd->getPrice(), true, false);
                $imageUrl   =   $this->_imageHelper->init($relatedPrd, 'product_page_image_small')
                                ->setImageFile($relatedPrd->getThumbnail()) // image,small_image,thumbnail
                                ->resize(100)
                                ->getUrl();

                $associatePrd .= '<option value="'.$relatedPrd->getId().'" data-image= "'.$imageUrl.'" data-price="'.$formattedPrice.'">'.$relatedPrd->getName().'</option>';

            }
        }

        return $associatePrd; 
    }
}
