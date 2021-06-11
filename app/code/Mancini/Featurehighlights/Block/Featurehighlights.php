<?php
namespace Mancini\Featurehighlights\Block;
use Magento\Framework\View\Element\Template;

class Featurehighlights extends Template
{
        /**
         * @var \Magento\Framework\Registry
        */
        protected $_registry;

        /**
        * @var attributeSet
        */
        protected $attributeSet;

        /**
        * @var $configFactory
        */
        protected $configFactory;
        

        /**
         * @param \Magento\Framework\View\Element\Template\Context $context
         * @param \Magento\Framework\Registry $registry
         * @param \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet
         * @param  \Magento\Catalog\Model\Config $configFactory
         */
        public function __construct(
            \Magento\Framework\View\Element\Template\Context $context,
            \Magento\Framework\Registry $registry,
            \Magento\Eav\Api\AttributeSetRepositoryInterface $attributeSet,
            \Magento\Catalog\Model\Config $configFactory,
            array $data = []
        ) {
            parent::__construct($context, $data);
            $this->_registry = $registry;
            $this->attributeSet = $attributeSet;
            $this->configFactory = $configFactory;
        }

        /**
         * Return catalog product object
         * @return \Magento\Catalog\Model\Product|Null
         */
        public function getCurrentProduct()
        {        
            return $this->_registry->registry('current_product');
        } 

        /**
         * Function for getting attribute set name of the current product
         * @return string
         */
        public function getAttributeSetName(){
                $attributeSetRepository = $this->attributeSet->get($this->getCurrentProduct()->getAttributeSetId());
                $attributeSetName   =$attributeSetRepository->getAttributeSetName();
            return  $attributeSetName;
        }

        /**
         * Function for getting attribute set ID of the current product
         * @return string
         */
        public function getAttributeSetId(){
                $attributeSetId = $this->getCurrentProduct()->getAttributeSetId();
            return $attributeSetId ;
        }

        /**
         * Function for getting attribute group ID of the current product
         * @return string
         */
        public function getAttributeGroupId($attributeSetId){
                $attributeGroupId= $this->configFactory->getAttributeGroupId($attributeSetId, 'Mattress attribute set');
            return $attributeGroupId;
        }

         /**
         * Function for getting unit of weight attribute of the current product
         * @return string
         */
        public function getWeightUnit()
        {
            return $this->_scopeConfig->getValue(
                    'general/locale/weight_unit',
                    \Magento\Store\Model\ScopeInterface::SCOPE_STORE);
        }

         public function getFeaturehighlights(){

              $featureHighlights = array();
              $attribute_set_id   =   $this->getAttributeSetId();
              $attribute_grp_id   =   $this->getAttributeGroupId($attribute_set_id);
              $productAttributes  =   $this->getCurrentProduct()->getAttributes();
              $i=0;

              foreach ($productAttributes as $attribute) {
                if ($attribute->isInGroup($attribute_set_id, $attribute_grp_id)) {
                    if ($attribute->getFrontend()->getValue($this->getCurrentProduct()) == 'Yes') {
                        $featureHighlights[$i]['attribute_code'] = $attribute->getAttributeCode();
                        $featureHighlights[$i]['attribute_label']=$attribute->getFrontendLabel();
                        
                    }

                }
                $i++;
              }
              return $featureHighlights;

        } 
}