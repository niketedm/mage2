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

namespace Anowave\Faq\Ui\DataProvider\Product\Form\Modifier;

/**
 * Class Review
 */
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Ui\Component\Form as Form;
use Magento\Ui\Component\Form\Element\Wysiwyg as Wysiwyg;
use Magento\Framework\UrlInterface;
use Magento\Framework\Module\Manager as ModuleManager;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;

/**
 * Faq modifier for catalog product form
 *
 * @api
 * @since 100.1.0
 */
class Faq extends AbstractModifier
{
	const GROUP_FAQ 		= 'faqs';
	const GROUP_CONTENT 	= 'content';
	const DATA_SCOPE_REVIEW = 'grouped';
	const SORT_ORDER 		= 20;
	const LINK_TYPE 		= 'associated';
	
	/**
	 * @var LocatorInterface
	 * 
	 * @since 100.1.0
	 */
	protected $locator;
	
	/**
	 * @var UrlInterface
	 * 
	 * @since 100.1.0
	 */
	protected $urlBuilder;
	
	/**
	 * @var \Anowave\Faq\Helper\Data
	 */
	protected $helper;
	
	/**
	 * @var RequestInterface
	 */
	protected $request;
	
	/**
	 * @var ModuleManager
	 */
	private $moduleManager;
	
	/**
	 * Constructor 
	 * 
	 * @param LocatorInterface $locator
	 * @param UrlInterface $urlBuilder
	 * @param \Anowave\Faq\Helper\Data $helper
	 */
	public function __construct
	(
		LocatorInterface $locator,
		UrlInterface $urlBuilder,
		\Anowave\Faq\Helper\Data $helper,
	    RequestInterface $request
	) 
	{
		/**
		 * Set locator
		 * 
		 * @var LocatorInterface $locator
		 */
		$this->locator = $locator;
		
		/**
		 * Set URL builder 
		 * 
		 * @var UrlInterface $urlBuilder
		 */
		$this->urlBuilder = $urlBuilder;
		
		/**
		 * Set helper 
		 * 
		 * @var \Anowave\Faq\Helper\Data $helper
		 */
		$this->helper = $helper;
		
		/**
		 * Set request interface 
		 * 
		 * @var RequestInterface $request
		 */
		$this->request = $request;
	}
	
	/**
	 * {@inheritdoc}
	 * @since 100.1.0
	 */
	public function modifyMeta(array $meta)
	{
		if (!$this->locator->getProduct()->getId() || !$this->getModuleManager()->isOutputEnabled('Anowave_Faq')) 
		{
			return $meta;
		}
		
		$meta[static::GROUP_FAQ] = 
		[
			'arguments' =>
			[
				'data' =>
				[
					'config' =>
					[
						'label' 		=> __('Frequently Asked Questions'),
						'collapsible' 	=> true,
						'opened' 		=> false,
						'componentType' => Form\Fieldset::NAME,
						'sortOrder' 	=> $this->getNextGroupSortOrder($meta,static::GROUP_CONTENT,static::SORT_ORDER),
					]
				]
			],
			'children' => 
			[
				'faq_listing' => 
				[
					'arguments' => 
					[
						'data' => 
						[
							'config' => 
							[
							    'displayAsLink'         => false,
								'autoRender' 			=> true,
							    'formElement'           => 'container',
								'componentType' 		=> 'insertListing',
								'dataScope' 			=> 'faq_listing',
								'externalProvider' 		=> 'faq_listing.faq_listing_data_source',
								'selectionsProvider' 	=> 'faq_listing.faq_listing.product_columns.ids',
								'ns' 					=> 'faq_listing',
							    'render_url' 			=> $this->urlBuilder->getUrl('mui/index/render',['current_product_id' => $this->getCurrentProductId(), 'current_store_id' => $this->getCurrentStoreId()]),
							    'realTimeLink' 			=> true,
								'behaviourType' 		=> 'simple',
								'externalFilterMode' 	=> true,
								'imports' => 
								[
									'productId' 	=> '${ $.provider }:data.product.current_product_id',
									'storeId'   	=> '${ $.provider }:data.product.current_store_id',
									'totalRecords' 	=> '${ $.provider }:data.totalRecords'
								],
								'exports' => 
								[
									'productId' => '${ $.externalProvider }:params.current_product_id',
									'storeId'   => '${ $.externalProvider }:params.current_store_id'
								],
							    'provider' => false,
							    'dataLinks' => 
							    [
							        'imports' => false,
							        'exports' => true
							    ],
							]
						]
					]
				],
				'holder.faq_store_id' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'formElement' 	=> 'hidden',
								'componentType' => 'field',
								'visible' 		=> 1,
								'required' 		=> 1,
								'value' 		=> $this->helper->getCurrentStoreId()
							]
						]
					]
				],
				'holder.faq_product_id' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'formElement' 	=> 'hidden',
								'componentType' => 'field',
								'visible' 		=> 1,
								'required' 		=> 1,
								'value' 		=> $this->locator->getProduct()->getId()
							]
						]
					]
				],
				'holder.faq_endpoint' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'formElement' 	=> 'hidden',
								'componentType' => 'field',
								'visible' 		=> 1,
								'required' 		=> 1,
								'value' => $this->urlBuilder->getUrl('faq/index/create')
							]
						]
					]
				],
				'holder.faq_enable' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'component' 	=> 'Magento_Ui/js/form/element/single-checkbox',
								'formElement' 	=> 'checkbox',
								'componentType' => 'field',
								'prefer' 		=> 'toggle',
								'visible' 		=> 1,
								'required' 		=> 1,
								'multiple'		=> false,
								'value'			=> '1',
								'label' 		=> __('Enable'),
								'notice'		=> __('Select status. Disabled FAQ will not appear on frontend.'),
								'checked'		=> true
							]
						]
					]
				],
				'holder.faq_question' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' => 
							[
								'formElement' 	=> 'input',
								'componentType' => 'field',
								'visible' 		=> 1,
								'required' 		=> 1,
								'label' 		=> __('Question'),
								'notice'		=> __('Enter frequenlty asked question')
							]
						]
					]
				],
				'faq_answer' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'dataType'		=> 'textarea',
								'formElement' 	=> 'wysiwyg',
								'componentType' => 'field',
								'wysiwyg'		=> true,
								'wysiwygConfigData' => 
								[
									'add_variables'          => false,
									'add_widgets' 	         => false,
									'add_directives'         => true,
									'use_container'          => true,
									'container_class'        => 'hor-scroll',
								    'is_pagebuilder_enabled' => false
  								],
								'elementTmpl'	=> 'ui/form/element/wysiwyg',
								'template'		=> 'ui/form/field',
								'visible' 		=> 1,
								'required' 		=> 1,
								'label' 		=> __('Answer'),
								'notice'		=> __('Enter question answer/clarification')
							]
						]
					]
				],
				'faq_submit' =>
				[
					'arguments' =>
					[
						'data' =>
						[
							'config' =>
							[
								'formElement' 	=> 'input',
								'componentType' => 'field',
								'elementTmpl'	=> 'Anowave_Faq/form',
								'visible' 		=> 1,
								'label'			=> ' '
							]
						]
					]
				]
			]
		];
		
		return $meta;
	}
	
	/**
	 * {@inheritdoc}
	 * @since 100.1.0
	 */
	public function modifyData(array $data)
	{
	    $data[$this->getCurrentProductId()][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $this->getCurrentProductId();
	    $data[$this->getCurrentProductId()][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->getCurrentStoreId();
	    
		return $data;
	}
	
	/**
	 * Get current product id
	 * 
	 * @return int
	 */
	public function getCurrentProductId()
	{
	    return (int) $this->locator->getProduct()->getId();
	}
	
	/**
	 * Get current store id
	 * 
	 * @return number
	 */
	protected function getCurrentStoreId()
	{	
		return $this->helper->getCurrentStoreId();
	}
	
	/**
	 * Retrieve module manager instance using dependency lookup to keep this class backward compatible.
	 *
	 * @return ModuleManager
	 *
	 * @deprecated 100.2.0
	 */
	private function getModuleManager()
	{
		if ($this->moduleManager === null) 
		{
			$this->moduleManager = ObjectManager::getInstance()->get(ModuleManager::class);
		}
		
		return $this->moduleManager;
	}
}
