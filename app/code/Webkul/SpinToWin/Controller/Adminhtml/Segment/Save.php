<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Controller\Adminhtml\Segment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;

class Save extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Logger\Logger $logger,
        \Magento\Customer\Model\GroupFactory $groupFactory,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Helper\Data $helper
    ) {
        parent::__construct($context);
        $this->_dateFilter = $dateFilter;
        $this->segmentsFactory = $segmentsFactory;
        $this->infoFactory = $infoFactory;
        $this->logger = $logger;
        $this->groupFactory = $groupFactory;
        $this->ruleFactory = $ruleFactory;
        $this->_jsonData = $jsonData;
        $this->helper = $helper;
    }

    public function execute()
    {
        try {
            $data = $this->getRequest()->getPostValue();
            $infoModel = $this->infoFactory->create()->load($data['spin_id']);
            if ($data['type']==1) {
                $ruleModel = $this->ruleFactory->create();
                $groupIds = [];
                $groups = $this->groupFactory->create()->getCollection()->toOptionArray();
                foreach ($groups as $group) {
                    $groupIds[] = $group['value'];
                }
                $ruleData = [];
                $ruleData['segment_id'] = 0;
                if ($data['rule_id']) {
                    $ruleData['rule_id'] = $data['rule_id'];
                    $ruleData['segment_id'] = $data['entity_id'];
                }
                $ruleData['name'] = $data['label'];
                $ruleData['is_active'] = 1;
                $ruleData['simple_action'] = $data['simple_action'];
                $ruleData['discount_amount'] = $data['discount_amount'];
                $ruleData['rule'] = $data['rule'];
                $ruleData['discount_qty'] = $data['discount_qty'];
                $ruleData['discount_step'] = $data['discount_step'];
                $ruleData['stop_rules_processing'] = $data['stop_rules_processing'];
                $ruleData['from_date'] = $data['from_date'];
                $ruleData['to_date'] = $data['to_date'];
                $ruleData['coupon_type'] = 2;
                $ruleData['use_auto_generation'] = 1;
                $ruleData['uses_per_coupon'] = 1;
                $ruleData['uses_per_customer'] = null;
                $ruleData['website_ids'] = $infoModel->getWebsiteIds();
                $ruleData['customer_group_ids'] = implode(',', $groupIds);

                if (isset($ruleData['simple_action']) && $ruleData['simple_action'] == 'by_percent' && isset($ruleData['discount_amount'])) {
                    $ruleData['discount_amount'] = min(100, $ruleData['discount_amount']);
                }

                if (isset($ruleData['rule']['conditions'])) {
                    $ruleData['conditions'] = $ruleData['rule']['conditions'];
                }

                if (isset($ruleData['rule']['actions'])) {
                    $ruleData['actions'] = $ruleData['rule']['actions'];
                }
                
                $ruleModel->loadPost($ruleData);
                $ruleModel->save();
            }

            $segmentData = [];
            $segmentData['rule_id'] = $data['rule_id'];
            $segmentData['spin_id'] = $data['spin_id'];
            $segmentData['type'] = $data['type'];
            $segmentData['heading'] = $data['heading'];
            $segmentData['label'] = $data['label'];
            $segmentData['description'] = $data['description'];
            $segmentData['limits'] = $data['limits'];
            $segmentData['gravity'] = $data['gravity'];
            $segmentData['position'] = $data['position'];
            if ($data['entity_id']) {
                $segmentData['entity_id'] = $data['entity_id'];
            }
            if (!$segmentData['rule_id']) {
                $segmentData['rule_id'] = 0;
            }
            if ($segmentData['limits']==='' || $segmentData['limits']===null) {
                $segmentData['limits'] = null;
            }
            $segment = $this->segmentsFactory->create();
            $segment->setData($segmentData);
            if (isset($ruleModel) && is_object($ruleModel) && $ruleModel->getRuleId()) {
                $segment->setRuleId($ruleModel->getRuleId());
            }
            $segment->save();
            if (isset($ruleModel) && is_object($ruleModel) && $ruleModel->getRuleId()) {
                $ruleModel->setSegmentId($segment->getId())->save();
            }
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->_jsonData
                ->jsonEncode(
                    [
                        'success' => 1,
                        'message' => __('Segment data successfully saved.')
                    ]
                ));
            return;
        } catch (\Exception $e) {
            $this->logger->info($e->getTraceAsString());
            $this->getResponse()->setHeader('Content-type', 'application/javascript');
            $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 0,
                            'message' => $e->getMessage()
                        ]
                    ));
            return;
        }
    }
    
    /**
     * check permission
     *
     * @return boolean
     */
    public function _isAllowed()
    {
        return $this->_authorization->isAllowed('Webkul_SpinToWin::manage');
    }

    public function _processUrlKeys()
    {
        return true;
    }
}
