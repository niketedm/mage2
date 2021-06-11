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
 * @copyright 	Copyright (c) 2019 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */
namespace Anowave\Faq\Block;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\View\Element\Template;

/**
 * Product Review Tab
 *
 * @author     Magento Core Team <core@magentocommerce.com>
 */
class Faq extends Template implements IdentityInterface
{
	protected $faqs = array();
	
	/**
	 * Core registry
	 *
	 * @var \Magento\Framework\Registry
	 */
	protected $_coreRegistry;
	
	/**
	 * Review resource model
	 *
	 * @var \Magento\Review\Model\ResourceModel\Review\CollectionFactory
	 */
	protected $factory;
	
	/**
	 * @var \Magento\Cms\Model\Template\FilterProvider
	 */
	protected $filterProvider;
	
	/**
	 * @var \Anowave\Faq\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var \Magento\Framework\Url\DecoderInterface
	 */
	protected $urlDecoder;
	
	/**
	 * Constructor
	 *
	 * @param \Magento\Framework\View\Element\Template\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory
	 * @param \Magento\Cms\Model\Template\FilterProvider $filterProvider
	 * @param \Anowave\Faq\Helper\Data $helper
	 * @param array $data
	 */
	public function __construct
	(
		\Magento\Framework\View\Element\Template\Context $context,
		\Magento\Framework\Registry $registry,
		\Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $collectionFactory,
		\Magento\Cms\Model\Template\FilterProvider $filterProvider,
		\Anowave\Faq\Helper\Data $helper,
		array $data = []
		)
	{
		/**
		 * Set registry
		 *
		 * @var \Magento\Framework\Registry $_coreRegistry
		 */
		$this->_coreRegistry = $registry;
		
		/**
		 * Set FAQ collection factory
		 *
		 * @var \Anowave\Faq\Model\ResourceModel\Item\CollectionFactory $factory
		 */
		$this->factory = $collectionFactory;
		
		parent::__construct($context, $data);
		
		/**
		 * Set tab title
		 */
		$this->setTabTitle();
		
		/**
		 * Set filter provider
		 *
		 * @var \Anowave\Faq\Block\Faq $filterProvider
		 */
		$this->filterProvider = $filterProvider;
		
		/**
		 * Set helper
		 *
		 * @var \Anowave\Faq\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		$this->urlDecoder = \Magento\Framework\App\ObjectManager::getInstance()->create('\Magento\Framework\Url\DecoderInterface');
	}
	
	/**
	 * Set tab title
	 */
	public function setTabTitle()
	{
		$this->setTitle
		(
			__('FAQs')
			);
	}
	
	/**
	 * Get faq
	 *
	 * @return array
	 */
	public function getFaqs()
	{
		if (!$this->faqs)
		{
			$collection = $this->factory
			->create()
			->addProduct($this->getProductId())->addFieldToFilter('faq_enable',1)
			->addFieldToFilter('faq_store_id', array('in' => [0, $this->getProduct()->getStore()->getId()]));
			
			foreach($collection as $item)
			{
				$this->faqs[] = $item;
			}
		}
		
		return $this->faqs;
	}
	
	/**
	 * Get filter provider
	 *
	 * @return \Magento\Cms\Model\Template\FilterProvider
	 */
	public function getFilterProvider()
	{
		return $this->filterProvider;
	}
	
	public function processDirectives($content)
	{
		if (false !== strpos($content, '__directive'))
		{
			@$doc = new \DOMDocument();
			@$doc->loadHTML($content);
			
			/**
			 * Get all images
			 *
			 * @var DOMNodeList $images
			 */
			$images = $doc->getElementsByTagName('img');
			
			foreach($images as $image)
			{
				/**
				 * Get image source
				 *
				 * @var string $source
				 */
				$source = $image->getAttribute('src');
				
				
				$params = explode('/', $source);
				
				$offset = array_search('___directive', $params);
				
				if (false !== $offset && isset($params[$offset + 1]))
				{
					$encode = $params[$offset + 1];
					
					$media = $this->urlDecoder->decode($encode);
					
					$media = $this->filterProvider->getBlockFilter()->filter($media);
					
					$content = str_replace($source, $media, $content);
				}
			}
		}
		
		return $content;
	}
	
	/**
	 * Get current product
	 *
	 * @return \Magento\Catalog\Model\Product
	 */
	public function getProduct()
	{
		return $this->_coreRegistry->registry('product');
	}
	
	/**
	 * Get current product id
	 *
	 * @return null|int
	 */
	public function getProductId()
	{
		return $this->getProduct()->getId();
	}
	
	/**
	 * Return unique ID(s) for each object in system
	 *
	 * @return array
	 */
	public function getIdentities()
	{
		return [\Anowave\Faq\Model\Item::CACHE_TAG];
	}
	
	public function _toHtml()
	{
		if (!$this->getFaqs())
		{
			return '';
		}
		
		return parent::_toHtml();
	}
}