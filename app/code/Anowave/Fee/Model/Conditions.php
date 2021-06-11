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
 
namespace Anowave\Fee\Model;

class Conditions extends \Magento\Rule\Model\AbstractModel
{
	/**
     * Prefix of model events names
     *
     * @var string
     */
    protected $_eventPrefix = 'ae_fee_rules';
	
	/**
	 * @var Rule\Condition\CombineFactory
	 */
	protected $conditionsFactory;
	
	/**
	 * @var \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory
	 */
	protected $combineFactory;
	
	/**
	 * Store already validated addresses and validation results
	 *
	 * @var array
	 */
	protected $validatedAddresses = [];

	/**
	 * Constructor 
	 * 
	 * @param \Magento\Framework\Model\Context $context
	 * @param \Magento\Framework\Registry $registry
	 * @param \Magento\Framework\Data\FormFactory $formFactory
	 * @param \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate
	 * @param \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory
	 * @param \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF
	 * @param \Magento\Framework\Model\ResourceModel\AbstractResource $resource
	 * @param \Magento\Framework\Data\Collection\AbstractDb $resourceCollection
	 * @param array $data
	 */
    public function __construct
    (
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\Framework\Data\FormFactory $formFactory,
        \Magento\Framework\Stdlib\DateTime\TimezoneInterface $localeDate,
        \Magento\SalesRule\Model\Rule\Condition\CombineFactory $condCombineFactory,
        \Magento\SalesRule\Model\Rule\Condition\Product\CombineFactory $condProdCombineF,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) 
    {
        $this->condCombineFactory = $condCombineFactory;
        $this->condProdCombineF = $condProdCombineF;
        
        parent::__construct($context, $registry, $formFactory, $localeDate, $resource, $resourceCollection, $data);
    }

    /**
     * Set resource model and Id field name
     *
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        
        $this->_init('Anowave\Fee\Model\ResourceModel\Conditions');
        
        $this->setIdFieldName('rule_id');
    }

	/**
     * Get rule condition combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Combine
     */
    public function getConditionsInstance()
    {
        return $this->condCombineFactory->create();
    }

    /**
     * Get rule condition product combine model instance
     *
     * @return \Magento\SalesRule\Model\Rule\Condition\Product\Combine
     */
    public function getActionsInstance()
    {
        return $this->condProdCombineF->create();
    }
}