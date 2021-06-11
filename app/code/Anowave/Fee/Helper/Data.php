<?php
/**
 * Anowave Magento 2 Extra Fee
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
 * @package 	Anowave_Fee
 * @copyright 	Copyright (c) 2020 Anowave (http://www.anowave.com/)
 * @license  	http://www.anowave.com/license-agreement/
 */

namespace Anowave\Fee\Helper;

use Anowave\Package\Helper\Package;

class Data extends \Anowave\Package\Helper\Package
{
	const FEE_ATTRIBUTE = 'fee_category';
	
	const FEE_TYPE_FIXED 				= 'F';
	const FEE_TYPE_PERCENTAGE 			= 'P';
	const FEE_TYPE_PERCENTAGE_PRODUCT 	= 'PP';
	const FEE_TYPE_FIXED_PRODUCT		= 'PF';
	const FEE_TYPE_FIXED_CATEGORY 		= 'FC';
	const FEE_TYPE_PERCENTAGE_CATEGORY	= 'PC';
	const FEE_TYPE_ONCE_PER_CATEGORY	= 'OC';
	const FEE_TYPE_ONCE_PER_CATEGORY_P  = 'OP';
	const FEE_TYPE_ONCE_PER_CATEGORY_PQ = 'OQ';
	
	const FEE_CONDITIONS_FORM_NAME = 'fee_fee_form';
	
	/**
	 * Package name
	 * @var string
	 */
	protected $package = 'MAGE2-FEE';
	
	/**
	 * Config path 
	 * @var string
	 */
	protected $config = 'fee/general/license';
	
	/**
	 * @var \Magento\Catalog\Model\ProductFactory
	 */
	protected $productFactory;
	
	/**
	 * @var \Anowave\Fee\Model\ConditionsFactory
	 */
	protected $conditionsFactory;
	
	/**
	 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory
	 */
	protected $conditionsCollectionFactory;
	
	/**
	 * @var \Magento\Catalog\Model\CategoryFactory
	 */
	protected $categoryFactory;
	
	/**
	 * @var \Magento\Quote\Model\QuoteFactory
	 */
	protected $quoteFactory;
	
	/**
	 * @var \Magento\Sales\Api\OrderRepositoryInterface
	 */
	protected $orderRepository;
	
	/**
	 * @var \Magento\Framework\App\Request\Http 
	 */
	protected $request;
	
	/**
	 * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable
	 */
	protected $configurableType;
	
	/**
	 * @var \Anowave\Fee\Model\ResourceModel\Fee\CollectionFactory
	 */
	protected $feeCollectionFactory;
	
	/**
	 * @var \Magento\Store\Model\StoreManagerInterface
	 */
	protected $storeManager;
	
	
	/**
	 * @var \Magento\Tax\Api\TaxCalculationInterface
	 */
	protected $taxCalculation;
	
	/**
	 * 
	 * @var \Magento\Customer\Model\Session
	 */
	protected $customerSession;

	/**
	 * @var \Magento\Tax\Model\Calculation\Rate
	 */
	protected $rate;
	
	/**
	 * @var \Anowave\Fee\Model\Conditions
	 */
	private $rule = null;

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\App\Helper\Context $context
	 * @param \Magento\Catalog\Model\ProductFactory $productFactory
	 * @param \Anowave\Fee\Model\ConditionsFactory $conditionsFactory
	 * @param \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory
	 * @param \Magento\Catalog\Model\CategoryFactory $categoryFactory
	 * @param \Magento\Quote\Model\QuoteFactory $quoteFactory
	 * @param \Magento\Sales\Api\OrderRepositoryInterface $orderRepository
	 * @param \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
	 * @param \Anowave\Fee\Model\ResourceModel\Fee\CollectionFactory $feeCollectionFactory
	 * @param \Magento\Tax\Model\TaxCalculation $taxCalculation
	 * @param \Magento\Store\Model\StoreManagerInterface $storeManager
	 * @param \Magento\Customer\Model\Session $customerSession
	 */
	public function __construct
	(
		\Magento\Framework\App\Helper\Context $context,
		\Magento\Catalog\Model\ProductFactory $productFactory,
		\Anowave\Fee\Model\ConditionsFactory $conditionsFactory,
		\Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory,
		\Magento\Catalog\Model\CategoryFactory $categoryFactory,
		\Magento\Quote\Model\QuoteFactory $quoteFactory,
		\Magento\Sales\Api\OrderRepositoryInterface $orderRepository,
		\Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType,
		\Anowave\Fee\Model\ResourceModel\Fee\CollectionFactory $feeCollectionFactory,
		\Magento\Tax\Model\TaxCalculation $taxCalculation,
		\Magento\Store\Model\StoreManagerInterface $storeManager,
		\Magento\Customer\Model\Session $customerSession,
	    \Magento\Tax\Model\Calculation\Rate $rate
	)
	{
		parent::__construct($context);
		
		/**
		 * Set request 
		 * 
		 * @var \Magento\Framework\App\Request\Http $request
		 */
		$this->request = $context->getRequest();
		
		/**
		 * Set product factory 
		 * 
		 * @var \Magento\Catalog\Model\ProductFactory
		 */
		$this->productFactory = $productFactory;
		
		/**
		 * Set conditions factory 
		 * 
		 * @var \Anowave\Fee\Model\ConditionsFactory
		 */
		$this->conditionsFactory = $conditionsFactory;
		
		/**
		 * Set conditions factory collection
		 * 
		 * @var \Anowave\Fee\Model\ResourceModel\Conditions\CollectionFactory $conditionsCollectionFactory
		 */
		$this->conditionsCollectionFactory = $conditionsCollectionFactory;
		
		/**
		 * Set category factory 
		 * 
		 * @var \Magento\Catalog\Model\CategoryFactory
		 */
		$this->categoryFactory = $categoryFactory;
		
		/**
		 * Set quote factory 
		 * 
		 * @var \Magento\Quote\Model\QuoteFactory $quoteFactory
		 */
		$this->quoteFactory = $quoteFactory;
		
		/**
		 * Set order repository 
		 * 
		 * @var \Anowave\Fee\Helper\Data $orderRepository
		 */
		$this->orderRepository = $orderRepository;
		
		/**
		 * Set configurable type
		 * 
		 * @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType
		 */
		$this->configurableType = $configurableType;
		
		/**
		 * Set fee collection factory 
		 * 
		 * @var \Anowave\Fee\Helper\Data $feeCollectionFactory
		 */
		$this->feeCollectionFactory = $feeCollectionFactory;
		
		/**
		 * Set tax calculation service 
		 * 
		 * @var \Magento\Tax\Model\TaxCalculation $taxCalculationService
		 */
		$this->taxCalculation = $taxCalculation;
		
		/**
		 * Set store manager 
		 * 
		 * @var \Anowave\Fee\Helper\Data $storeManager
		 */
		$this->storeManager = $storeManager;
		
		/**
		 * Set customer session
		 * 
		 * @var \Anowave\Fee\Helper\Data $customerSession
		 */
		$this->customerSession = $customerSession;
		
		/**
		 * Set rate 
		 * 
		 * @var \Magento\Tax\Model\Calculation\Rate $rate
		 */
		$this->rate = $rate;
	}
	

	/**
	 * Check if fee is active
	 * 
	 * @return boolean
	 */
	public function isActive()
	{
	    return 1 === (int) $this->getConfig('fee/general/active');
	}
	
	/**
	 * Render fee in cart
	 * 
	 * @return bool
	 */
	public function renderFeeInCart() : bool
	{
	    switch((int) $this->getConfig('fee/fee/render_in_cart'))
	    {
	        case \Anowave\Fee\Model\System\Config\Source\Render::SHOW_NONE: return false;
	        case \Anowave\Fee\Model\System\Config\Source\Render::SHOW_ALL: return true;
	        case \Anowave\Fee\Model\System\Config\Source\Render::SHOW_LOGGED:
	            
	            if ($this->isLoggedIn())
	            {
	                return true;
	            }
	            else 
	            {
	                return false;
	            }
	            
	            break;
	    }
	    return false;
	}
	
	/**
	 * Get fee name
	 * 
	 * @return string
	 */
	public function getFeeName()
	{
	    if ($name = $this->getConfig('fee/fee/name'))
	    {
	        return __($name);
	    }
	    
	    return __('Custom fee');
	}
	
	/**
	 * Get fee amount
	 *
	 * @return float
	 */
	public function getFeeAmount()
	{
		return (float) $this->getConfig('fee/fee/amount');
	}
	
	/**
	 * Get Fee 
	 * 
	 * @param \Magento\Quote\Model\Quote $quote
	 */
	public function getFee(\Magento\Quote\Model\Quote $quote = null)
	{
		if (is_null($quote))
		{
			if ($this->request->getParam('order_id'))
			{
				$order = $this->orderRepository->get($this->request->getParam('order_id'));
				
				if ($order->getQuoteId())
				{
					$quote = $this->quoteFactory->create()->load
					(
						$order->getQuoteId()
					);
				}
			}
		}
		
		return call_user_func_array($this->calculator($quote), array($quote));
	}
	
	public function getFeeTooltip()
	{
		return __('This is the tooltip');
	}
	
	/**
	 * Get fee tax
	 * 
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return number
	 */
	public function getFeeTax(\Magento\Quote\Model\Quote $quote = null)
	{
	    /**
	     * Get all tax rates 
	     * 
	     * @var array $rates
	     */
	    $rates = $this->getTaxRates();
	    
	    /**
	     * Check if shipping country is taxable 
	     * 
	     * @var array $taxable
	     */
	    $taxable = array_filter($rates, function($rate) use ($quote) 
	    {
	        return $rate['tax_country_id'] === $quote->getShippingAddress()->getCountry();
	    });

	    if ($taxable)
	    {
	        $rate = (float) array_shift($taxable)['rate'];

	        if ($rate <= 0)
	        {
	            return 0;
	        }
	    }
	    else 
	    {
	        return 0;
	    }
	    
		/**
		 * Get fee tax class
		 * 
		 * @var int $tax_class_id
		 */
		$tax_class_id = (int) $this->getConfig('fee/tax/class');

		/**
		 * Default tax rate 
		 * 
		 * @var integer $rate
		 */
		$rate = 0;
		
		if ($tax_class_id)
		{
			$rate = $this->taxCalculation->getCalculatedRate($tax_class_id, $this->customerSession->getCustomerId(), $this->storeManager->getStore()->getId());
		}

		$fee = $this->getFee($quote);
		
		if ($rate)
		{
			return round(($fee * $rate)/100,2);
		}
		
		return 0;
	}
	
	/**
	 * Create a calculation closure 
	 * 
	 * @param \Magento\Quote\Model\Quote $quote
	 */
	public function calculator(\Magento\Quote\Model\Quote $quote = null)
	{
		$type = $this->getConfig('fee/fee/type');

		$this->validateConditions($quote);
		
		/**
		 * Get fee
		 *
		 * @var float
		 */
		$fee = (float) $this->getFeeAmount();
		
		/**
		 * Set base fee
		 * 
		 * @var float $fee_base
		 */
		$fee_base = $fee;
		
		/**
		 * Check if product falls into particular condition
		 */
		$use_quantity = (int) $this->getConfig('fee/fee/calculate_quantity');
		
		/**
		 * Calculate fee per product
		 */
		if (1 === (int) $this->getConfig('fee/fee/calculate_per_product'))
		{
			/**
			 * Reset fee for product based fee calculation
			 */
			$fee = 0;
			
			if (!$this->validateConditions($quote))
			{
				/**
				 * Try to validate fee
				 */
				return function(\Magento\Quote\Model\Quote $quote = null) use ($fee)
				{
					return 0;
				};
			}
			
			$attribute = $this->getConfig('fee/fee/product_fee_attribute');
			
			foreach ($quote->getAllVisibleItems() as $item)
			{
				$product = $this->getItemProduct($item);

				if (true)
				{
					if (\Anowave\Fee\Model\Attribute::NONE === $attribute)
					{
						$product_fee = $fee_base;
					}
					else 
					{
						/**
						 * Read product fee
						 * 
						 * @var float
						 */
						$product_fee = (float) $product->getData($attribute);
					}
					
					/**
					 * Validate conditions by quote
					 */
					if (!$this->validateConditions($quote))
					{
						$product_fee = 0;
					}
					
					/**
					 * Validate conditions by product and reset fee if conditions do not match
					 */
					if (!$this->validateConditionsEntity($quote, $product))
					{
						$product_fee = 0;
					}

					if (0 != $product_fee)
					{
						switch($type)
						{
							case self::FEE_TYPE_FIXED_PRODUCT:
								
								if ($use_quantity)
								{
									$fee += ($fee_base * (float) $item->getQty());
								}
								
								$fee += ($product_fee * ($use_quantity ? $item->getQty() : 1));
								break;
							case self::FEE_TYPE_PERCENTAGE_PRODUCT: 
								
								$fee_percent = ($product_fee * $item->getPrice())/100;
								
								if ($use_quantity)
								{
									$fee_percent *= (float) $item->getQty();
								}
								
								$fee += ($fee_percent * ($use_quantity ? $item->getQty() : 1));
								break;
							default: 
								$fee += ($product_fee * ($use_quantity ? $item->getQty() : 1));
								break;
						}
					}
					else 
					{
						/**
						 * Collect category fees
						 * 
						 * @var []
						 */
						$categories = [];
						
						foreach ($product->getCategoryIds() as $id)
						{
							$category = $this->categoryFactory->create()->load($id);
							
							$categories[(int) $category->getId()] = (float) $category->getData(self::FEE_ATTRIBUTE);
						}
						
						
						if (\Magento\Catalog\Model\Product\Type::TYPE_SIMPLE == $product->getTypeId() || \Magento\Catalog\Model\Product\Type::TYPE_VIRTUAL)
						{
							$parents = $this->configurableType->getParentIdsByChild
							(
								$product->getId()
							);
							
							if ($parents)
							{
								foreach ($parents as $identifier)
								{
									$parent = $this->productFactory->create()->load($identifier);
									
									foreach ($parent->getCategoryIds() as $parentCategoryId)
									{
										$category = $this->categoryFactory->create()->load($parentCategoryId);
										
										$categories[(int) $category->getId()] = (float) $category->getData(self::FEE_ATTRIBUTE);
									}
								}
							}
						}
						
						foreach ($categories as $category_fee)
						{
							switch ($type)
							{
								case self::FEE_TYPE_FIXED_CATEGORY:
							
									$fee += ($use_quantity ? $item->getQty() * $category_fee : $category_fee);
							
									break;
								case self::FEE_TYPE_PERCENTAGE_CATEGORY:
							
									$fee_percent = ($category_fee * $item->getPrice())/100;
									
									$fee += ($fee_percent * ($use_quantity ? $item->getQty() : 1));
							
									break;
							}
						}
					}
				}
			}
			
			$fee += $this->getCombinedFees($quote);
			
			return function(\Magento\Quote\Model\Quote $quote = null) use ($fee)
			{
				return $fee;
			};
		}
		else 
		{
			if (!$this->validateConditions($quote))
			{
				return function(\Magento\Quote\Model\Quote $quote = null) use ($fee)
				{
					return 0;
				};
			}
			
			switch($type)
			{
				case self::FEE_TYPE_PERCENTAGE: 
				{
					$fee += $this->getCombinedFees($quote);
					
					return function(\Magento\Quote\Model\Quote $quote = null) use ($fee)
					{
						$total = (float) $quote->getBaseSubtotal();
					
						if (false)
						{
							if ($quote->getShippingAddress())
							{
								$total += $quote->getShippingAddress()->getBaseShippingAmount();
							}
						}
	
						return ($total * $fee)/100;
					};
				}
				case self::FEE_TYPE_ONCE_PER_CATEGORY:
					
					$once = [];
					
					foreach ($quote->getAllVisibleItems() as $item)
					{
						$product = $this->productFactory->create()->load($item->getProduct()->getId());
						
						/**
						 * Collect category fees
						 *
						 * @var []
						 */
						$categories = [];
						
						foreach ($product->getCategoryIds() as $id)
						{
							$category = $this->categoryFactory->create()->load($id);
								
							$categories[(int) $category->getId()] = (float) $category->getData(self::FEE_ATTRIBUTE);
						}

						foreach ($categories as $category_id => $category_fee)
						{
							$once[$category_id] = $category_fee;
						}
					}
					
					$fee += $this->getCombinedFees($quote);

					return function(\Magento\Quote\Model\Quote $quote = null) use ($fee, $once)
					{
						return $fee + array_sum($once);
					};
					
					break;
				case self::FEE_TYPE_ONCE_PER_CATEGORY_P:
					
					$once = [];
						
					foreach ($quote->getAllVisibleItems() as $item)
					{
						$product = $this->productFactory->create()->load($item->getProduct()->getId());
					
						/**
						 * Collect category fees
						 *
						 * @var []
						*/
						$categories = [];
					
						foreach ($product->getCategoryIds() as $id)
						{
							$category = $this->categoryFactory->create()->load($id);
					
							$categories[(int) $category->getId()] = (float) $category->getData(self::FEE_ATTRIBUTE);
						}
					
						foreach ($categories as $category_id => $category_fee)
						{
							$fee_percent = ($category_fee * $item->getPrice())/100;
							
							$once[$category_id] = $fee_percent;
						}
						
						$fee += $this->getCombinedFees($quote);
					
						return function(\Magento\Quote\Model\Quote $quote = null) use ($fee, $once)
						{
							return $fee + array_sum($once);
						};
					}
					
					break;
				case self::FEE_TYPE_ONCE_PER_CATEGORY_PQ:
							
						$once = [];
					
						foreach ($quote->getAllVisibleItems() as $item)
						{
							$product = $this->productFactory->create()->load($item->getProduct()->getId());
								
							/**
							 * Collect category fees
							 *
							 * @var []
							*/
							$categories = [];
								
							foreach ($product->getCategoryIds() as $id)
							{
								$category = $this->categoryFactory->create()->load($id);
									
								$categories[(int) $category->getId()] = (float) $category->getData(self::FEE_ATTRIBUTE);
							}
								
							foreach ($categories as $category_id => $category_fee)
							{
								$fee_percent = ($category_fee * $item->getPrice())/100;
									
								$once[$category_id] = $fee_percent * $item->getQty();
							}
							
							$fee += $this->getCombinedFees($quote);
								
							return function(\Magento\Quote\Model\Quote $quote = null) use ($fee, $once)
							{
								return $fee + array_sum($once);
							};
						}
							
					break;

				default: 
				{
					$fee += $this->getCombinedFees($quote);
					
					return function(\Magento\Quote\Model\Quote $quote = null) use ($fee)
					{
						return $fee;
					};
				}
			}
		}
	}
	
	/**
	 * Get combined fees amount (added to global fee)
	 * 
	 * @param \Magento\Quote\Model\Quote $quote
	 * @return number
	 */
	private function getCombinedFees(\Magento\Quote\Model\Quote $quote = null)
	{
		$amount = 0;
		
		if ($quote)
		{
			$fees = $this->feeCollectionFactory->create();
			
			$combined = [];

			if ($fees->getSize())
			{
				foreach ($fees as $fee)
				{
				    if ($fee->getFeeCalculatePerProduct())
				    {
				        $amount += $this->calculateCombinedFeePerProduct($fee, $quote);
				    }
				    else 
				    {
				        if (\Anowave\Fee\Model\Fee::STATUS_ENABLED === (int) $fee->getFeeStatus())
				        {
				            /**
				             * Skip fee if applied for logged customers only
				             */
				            if (1 === (int) $fee->getFeeApplyLoggedOnly() && !$this->customerSession->isLoggedIn())
				            {
				                continue;
				            }
				            
				            /**
				             * Skio fee if applied for specific group only
				             */
				            if (!in_array((int) $fee->getFeeApplyGroupOnly(), [\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID,\Magento\Customer\Model\Group::CUST_GROUP_ALL]))
				            {
				                if ((int) $fee->getFeeApplyGroupOnly() !== (int) $this->getCustomerGroupId())
				                {
				                    continue;
				                }
				            }
				            
				            /**
				             * Load fee rule(s)
				             */
				            $rules = $this->conditionsCollectionFactory->create()->addFieldToFilter('rule_fee_id', $fee->getId());
				            
				            if ($rules->getSize())
				            {
				                $quote->setData('quote', $quote);
				            }
				            
				            switch($fee->getFeeType())
				            {
				                case self::FEE_TYPE_PERCENTAGE:
				                    {
				                        $total = (float) $quote->getBaseSubtotal();
				                        
				                        if ($quote && $rules->getSize())
				                        {
				                            $valid = true;
				                            
				                            foreach ($rules as $rule)
				                            {
				                                if (!$rule->getConditions()->validate($quote))
				                                {
				                                    $valid = false;
				                                }
				                            }
				                            
				                            if ($valid)
				                            {
				                                $combined[] = $fee->getData();
				                                
				                                $amount += ($total * $fee->getFee())/100;
				                            }
				                        }
				                        else
				                        {
				                            $combined[] = $fee->getData();
				                            
				                            $amount += ($total * $fee->getFee())/100;
				                        }
				                    };
				                    break;
				                case self::FEE_TYPE_FIXED:
				                    {
				                        if ($rules->getSize())
				                        {
				                            $valid = true;
				                            
				                            foreach ($rules as $rule)
				                            {
				                                if (!$rule->validate($quote))
				                                {
				                                    $valid = false;
				                                }
				                            }
				                            
				                            if ($valid)
				                            {
				                                $combined[] = $fee->getData();
				                                
				                                $amount += $fee->getFee();
				                            }
				                        }
				                        else
				                        {
				                            $combined[] = $fee->getData();
				                            
				                            $amount += $fee->getFee();
				                        }
				                    };
				                    break;
				            }
				            
				            $amount_quantity = 0;
				            
				            if (1 === (int) $fee->getFeeMultiplyQuantity())
				            {
				                foreach ($quote->getAllVisibleItems() as $item)
				                {
				                    $amount_quantity += (float) $item->getQty();
				                }
				                
				                $amount += ($amount_quantity * $amount) - $amount;
				            }
				        }
				    }
				}
			}
		}

		return $amount;
	}
	
	/**
	 * Calculate combined fee per product
	 * 
	 * @param \Anowave\Fee\Model\Fee $fee
	 * @param \Magento\Quote\Model\Quote $quote
	 */
	public function calculateCombinedFeePerProduct(\Anowave\Fee\Model\Fee $fee, \Magento\Quote\Model\Quote $quote = null)
	{
	    /**
	     * List of combined rules
	     * 
	     * @var array $combined
	     */
	    $combined = [];
	    
	    /**
	     * Default fee
	     * 
	     * @var integer $amount
	     */
	    $amount = 0;
	    
	    /**
	     * Loop visible quote items
	     */
	    foreach ($quote->getAllVisibleItems(true) as $item)
	    {
	        /**
	         * Skip fee if applied for logged customers only
	         */
	        if (1 === (int) $fee->getFeeApplyLoggedOnly() && !$this->customerSession->isLoggedIn())
	        {
	            continue;
	        }
	        
	        /**
	         * Skio fee if applied for specific group only
	         */
	        if (!in_array((int) $fee->getFeeApplyGroupOnly(), [\Magento\Customer\Model\Group::NOT_LOGGED_IN_ID,\Magento\Customer\Model\Group::CUST_GROUP_ALL]))
	        {
	            if ((int) $fee->getFeeApplyGroupOnly() !== (int) $this->getCustomerGroupId())
	            {
	                continue;
	            }
	        }
	        
	        /**
	         * Load fee rule(s)
	         */
	        $rules = $this->conditionsCollectionFactory->create()->addFieldToFilter('rule_fee_id', $fee->getId());
	        
	        if ($rules->getSize())
	        {
	            $quote->setData('quote', $quote);
	        }
	        
	        switch($fee->getFeeType())
	        {
	            case self::FEE_TYPE_PERCENTAGE:
	                {
	                    $total = (float) $item->getPrice(true);

	                    if (1 === (int) $fee->getFeeMultiplyQuantity())
	                    {
	                        $total *= (float) $item->getQty();
	                    }
	                    
	                    if ($quote && $rules->getSize())
	                    {
	                        $valid = true;
	                        
	                        foreach ($rules as $rule)
	                        {
	                            if (!$rule->getConditions()->validateByEntityId($item->getProduct()->getId()))
	                            {
	                                $valid = false;
	                            }
	                        }

	                        if ($valid)
	                        {
	                            $amount += ($total * $fee->getFee())/100;
	                        }
	                    }
	                    else
	                    {
	                        $amount += ($total * $fee->getFee())/100;
	                    }
	                };
	                break;
	            case self::FEE_TYPE_FIXED:
	                {
	                    if ($rules->getSize())
	                    {
	                        $valid = true;
	                        
	                        foreach ($rules as $rule)
	                        {
	                            if (!$rule->getConditions()->validateByEntityId($item->getProduct()->getId()))
	                            {
	                                $valid = false;
	                            }
	                        }
	                        
	                        if ($valid)
	                        {
	                            $fee_amount = $fee->getFee();
	                            
	                            if (1 === (int) $fee->getFeeMultiplyQuantity())
	                            {
	                                $fee_amount *= (float) $item->getQty();
	                            }
	                            
	                            $amount += $fee_amount;
	                        }
	                    }
	                    else
	                    {
	                        $fee_amount = $fee->getFee();
	                        
	                        if (1 === (int) $fee->getFeeMultiplyQuantity())
	                        {
	                            $fee_amount *= (float) $item->getQty();
	                        }
	                        
	                        $amount += $fee_amount;
	                    }
	                };
	                break;
	        }
	    }
	    
	    return $amount;
	}
	
	/**
	 * Get simple product from quote item 
	 * 
	 * @param \Magento\Quote\Model\Quote\Item $item
	 * @return \Magento\Catalog\Model\Product
	 */
	private function getItemProduct(\Magento\Quote\Model\Quote\Item $item)
	{
		if (\Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE == $item->getProduct()->getTypeId())
		{
			if ($option = $item->getOptionByCode('simple_product'))
			{
				if ($option->getProduct())
				{
					$product = $this->productFactory->create()->load
					(
						$option->getProduct()->getId()
					);
				}
				else
				{
					$product = $this->productFactory->create()->load
					(
						$item->getProduct()->getId()
					);
				}
			}
			else
			{
				$product = $this->productFactory->create()->load
				(
					$item->getProduct()->getId()
				);
			}
		}
		else
		{
			$product = $this->productFactory->create()->load
			(
				$item->getProduct()->getId()
			);
		}
		
		return $product;
	}
	
	/**
	 * Validate conditions
	 * 
	 * @return boolean
	 */
	private function validateConditions(\Magento\Quote\Model\Quote $quote = null)
	{
		/**
		 * Check if any fee rule exists and check if conditions are satisfied
		 */
		try
		{
			if ($this->getRule()->getId())
			{
				$quote->setQuote($quote);

				if (!$this->getRule()->validate($quote))
				{
					return false;
				}
			}
		}
		catch (\Exception $e){}
		
		return true;
	}
	
	/**
	 * Validate conditions for single entity 
	 * 
	 * @param \Magento\Quote\Model\Quote $quote
	 * @param \Magento\Catalog\Model\Product $product
	 * @return boolean
	 */
	private function validateConditionsEntity(\Magento\Quote\Model\Quote $quote = null, \Magento\Catalog\Model\Product $product)
	{
		return $this->validateConditionsEntityId
		(
			$product->getId()
		);
	}
	
	/**
	 * Validate conditions for single entity id 
	 * 
	 * @param number $entity_id
	 * @return boolean
	 */
	private function validateConditionsEntityId($entity_id = 0)
	{
		try
		{
			if ($this->getRule()->getId())
			{
				if (!$this->getRule()->getConditions()->validateByEntityId($entity_id))
				{
					return false;
				}
			}
		}
		catch (\Exception $e){}
		
		return true;
	}

	/**
	 * Get rule
	 * 
	 * @return \Anowave\Fee\Model\Conditions
	 */
	private function getRule()
	{
		if (!$this->rule)
		{
			$collection = $this->conditionsCollectionFactory
							   ->create()
			                   ->addFieldToFilter('rule_default',\Anowave\Fee\Observer\Config::DEFAULT_CONDITION_VALUE)
							   ->addFieldToFilter('rule_fee_store_id', $this->getCurrentScopeStoreId());

			if ($collection->getSize())
			{
				$this->rule = $collection->getFirstItem();
			}
			else 
			{
				$this->rule = $this->conditionsFactory->create();
			}
		}
		
		return $this->rule;
	}
	
	/**
	 * Get current website id
	 *
	 * @return number
	 */
	public function getCurrentScopeWebsiteId()
	{
		if (null !== $store = $this->getRequest()->getParam('website'))
		{
			return (int) $store;
		}
		else if ($this->storeManager->getWebsite()->getId())
		{
			return (int) $this->storeManager->getWebsite()->getId();
		}
		
		return 0;
	}
	
	/**
	 * Get current store id
	 *
	 * @return number
	 */
	public function getCurrentScopeStoreId()
	{
		if (null !== $store = $this->getRequest()->getParam('store'))
		{
			return (int) $store;
		}
		else if ($this->storeManager->getStore()->getId())
		{
			return (int) $this->storeManager->getStore()->getId();
		}
		
		return 0;
	}
	
	/**
	 * Get customer 
	 * 
	 * @return \Magento\Customer\Model\Customer
	 */
	public function getCustomer()
	{
	    return $this->customerSession->getCustomer();
	}
	
	/**
	 * Get customer group id
	 * 
	 * @return number
	 */
	public function getCustomerGroupId() : int
	{
	    return (int) $this->getCustomer()->getGroupId();
	}
	
	/**
	 * Get request
	 *
	 * @return \Magento\Framework\App\RequestInterface
	 */
	public function getRequest()
	{
		return $this->request;
	}
	
	/**
	 * Get tax rates 
	 * 
	 * @return array
	 */
	public function getTaxRates() : array
	{
	    $rates = [];
	    
	    foreach ($this->rate->getCollection()->getData() as $tax) 
	    {
	        $rates[$tax['tax_calculation_rate_id']] = $tax;
	    }
	    
	    return $rates;
	}
	
	/**
	 * Check if customer is logged in 
	 * 
	 * @return bool
	 */
	public function isLoggedIn() : bool
	{
	    return $this->customerSession->isLoggedIn();
	}
}