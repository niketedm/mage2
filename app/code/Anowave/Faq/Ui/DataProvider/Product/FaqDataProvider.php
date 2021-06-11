<?php
/**
 * Anowave Magento 2 Frequently Asked Questions
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Anowave license that is
 * available through the world-wide-web at this URL:
 * http://www.anowave.com/license-agreement/
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category 	Anowave
 * @package 	Anowave_Faq
 * @copyright 	Copyright (c) 2018 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Faq\Ui\DataProvider\Product;

use Magento\Framework\App\RequestInterface;
use Magento\Ui\DataProvider\AbstractDataProvider;
use Anowave\Faq\Model\ResourceModel\Item\CollectionFactory;
use Anowave\Faq\Model\Item;
use Magento\Framework\UrlInterface;

/**
 * Class ReviewDataProvider
 *
 * @api
 *
 * @method Collection getCollection
 * @since 100.1.0
 */
class FaqDataProvider extends AbstractDataProvider
{
    /**
     * @var CollectionFactory
     * @since 100.1.0
     */
    protected $collectionFactory;

    /**
     * @var RequestInterface
     * 
     * @since 100.1.0
     */
    protected $request;
    
    /**
     * @var \Magento\Cms\Model\Template\FilterProvider
     */
    protected $filterProvider;
    
    /**
     * @var \Anowave\Faq\Helper\Data
     */
    protected $helper;
    
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;
    
    /**
     * Constructor 
     * 
     * @param string $name
     * @param string $primaryFieldName
     * @param string $requestFieldName
     * @param CollectionFactory $collectionFactory
     * @param RequestInterface $request
     * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
     * @param \Anowave\Faq\Helper\Data $helper
     * @param UrlInterface $urlBuilder
     * @param array $meta
     * @param array $data
     */
    public function __construct
    (
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $collectionFactory,
        RequestInterface $request,
    	\Magento\Cms\Model\Template\FilterProvider $filterProvider,
    	\Anowave\Faq\Helper\Data $helper,
        UrlInterface $urlBuilder,
        array $meta = [],
        array $data = []
    ) 
    {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data);
        
        /**
         * Set collection factory 
         * 
         * @var CollectionFactory $collectionFactory
         */
        $this->collectionFactory = $collectionFactory;
        
        /**
         * Create collection
         * 
         * @var \Anowave\Faq\Ui\DataProvider\Product\FaqDataProvider $collection
         */
        $this->collection = $this->collectionFactory->create();
        
        /**
         * Set request 
         * 
         * @var RequestInterface $request
         */
        $this->request = $request;
        
        /**
         * Set filter provider 
         * 
         * @var \Magento\Cms\Model\Template\FilterProvider $filterProvider
         */
        $this->filterProvider = $filterProvider;
        
        /**
         * Set helper
         * 
         * @var \Anowave\Faq\Helper\Data $helper
         */
        $this->helper = $helper;
        
        /**
         * Set URL builder 
         * 
         * @var UrlInterface $urlBuilder
         */
        $this->urlBuilder = $urlBuilder;
    }

    /**
     * {@inheritdoc}
     * 
     * @since 100.1.0
     */
    public function getData()
    {
    	/**
    	 * Filter by current product id
    	 */
    	$this->getCollection()->addFieldToFilter('faq_product_id', $this->request->getParam('current_product_id', 0));
    	
    	/**
    	 * Get current store 
    	 * 
    	 * @var int $store
    	 */
    	$store = $this->request->getParam('current_store_id', 0);
    	
    	if ($store)
    	{
    		$this->getCollection()->addFieldToFilter('faq_store_id', $store);
    	}
    	
        $arrItems = 
        [
            'totalRecords' => $this->getCollection()->getSize(), 'items' => [],
        ];

        foreach ($this->getCollection() as $item) 
        {
        	$data = $item->toArray([]);
        	
        	if (isset($data['faq_content']))
        	{
        		$data['faq_content'] = $this->filterProvider->getBlockFilter()->filter($data['faq_content']);
        	}	
        	
        	$data['faq_enable'] = 1 === (int) $data['faq_enable'] ? __('Enabled') : __('Disabled');
        	
            $arrItems['items'][] = $data;
        }   

        return $arrItems;
    }
}
