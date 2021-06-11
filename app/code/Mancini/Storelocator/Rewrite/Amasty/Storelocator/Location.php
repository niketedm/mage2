<?php
    /**
	 * Amasty Storelocator Rewrite Model
	 *
	 * @category    Amasty
	 * @package     Mancini_Storelocator
	 * @author      Mancini
	 *
	 */
    declare(strict_types=1);

    namespace Mancini\Storelocator\Rewrite\Amasty\Storelocator;

    use Amasty\Storelocator\Api\ReviewRepositoryInterface;
    use Magento\Customer\Api\CustomerRepositoryInterface;
    use Magento\Framework\Escaper;
    use Amasty\Storelocator\Model\ConfigProvider;
    use Amasty\Storelocator\Model\ImageProcessor;

    class Location extends \Amasty\Storelocator\Model\Location
    {
        /**
         * @var \Mancini\Storelocator\Helper\Data
         */
        protected $_storelocHelper;

        public function __construct(
            \Magento\Framework\Model\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Framework\Data\FormFactory $formFactory,
            \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
            \Magento\Store\Model\StoreManagerInterface $storeManager,
            \Amasty\Base\Model\Serializer $serializer,
            \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $_condProdCombineF,
            \Magento\CatalogRule\Model\Rule\Condition\CombineFactory $locatorConditionFactory,
            ImageProcessor $imageProcessor,
            ConfigProvider $configProvider,
            \Amasty\Storelocator\Helper\Data $dataHelper,
            ReviewRepositoryInterface $reviewRepository,
            CustomerRepositoryInterface $customerRepository,
            Escaper $escaper,
            \Amasty\Storelocator\Model\ConfigHtmlConverter $configHtmlConverter,
            \Magento\Cms\Model\Template\FilterProvider $filterProvider,
            \Magento\Catalog\Model\ResourceModel\Product\CollectionFactory $productCollectionFactory,
            \Magento\Framework\Model\ResourceModel\Iterator $resourceIterator,
            \Magento\Catalog\Model\ProductFactory $productFactory,
            \Mancini\Storelocator\Helper\Data $storelocHelper,
            array $data = []
        ){
            $this->_storelocHelper  =   $storelocHelper;
            parent::__construct( $context,
            $registry,
            $formFactory,
            $localeDate,$storeManager, $serializer, $_condProdCombineF,$locatorConditionFactory, $imageProcessor, $configProvider, $dataHelper, $reviewRepository, $customerRepository, $escaper, $configHtmlConverter, $filterProvider, $productCollectionFactory, $resourceIterator, $productFactory,  $data);
        }
        /**
         * Optimized get data method
         *
         * @return array
         */
        public function getFrontendData(): array
        {  
        
            $storeHours = $this->getDataByKey('attributes'); // getting store hours

            if(!isset($storeHours['store_hours']))
            {
                $storeHours['store_hours'] ['option_title']['0'] = ''; 
                $storeHours['store_hours'] ['option_title']['1'] =  '';
            }

            $result = [
                'id' => (int)$this->getDataByKey('id'),
                'lat' => $this->getDataByKey('lat'),
                'lng' => $this->getDataByKey('lng'),
                'zip' => $this->getDataByKey('zip'),
                'address'=>$this->getDataByKey('address'),
                'state'=>$this->_storelocHelper->getRegionDataByName($this->getDataByKey('state')),
                'popup_html' => $this->getDataByKey('popup_html'),
                'phone'=>$this->getDataByKey('phone'),
                'distance'=>round($this->getDataByKey('distance'),1),
                'name' =>$this->getDataByKey('name'),
                'city' =>$this->getDataByKey('city'),
                'url_key' =>$this->getDataByKey('url_key'),
                 'store_hours_weekdays' => $storeHours['store_hours'] ['option_title']['0'],
                 'store_hours_weekend_days' => $storeHours['store_hours'] ['option_title']['1']
            ];

            if ($this->getDataByKey('marker_url')) {
                $result['marker_url'] = $this->getDataByKey('marker_url');
            }

            return $result;
        }
    }