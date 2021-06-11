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

namespace Webkul\SpinToWin\Block\Adminhtml;

/**
 * Adminhtml product grid block
 */
class Report extends \Magento\Backend\Block\Widget\Grid\Extended
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\SpinToWin\Model\ReportsFactory $reportsFactory,
        \Magento\Framework\Pricing\Helper\Data $pricing,
        array $data = []
    ) {
        $this->reportsFactory = $reportsFactory;
        $this->pricing = $pricing;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spintowin_report_grid');
        $this->setDefaultSort('position', 'asc');
        $this->setUseAjax(true);
    }

    /**
     * Apply various selection filters to prepare the sales order grid collection.
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId);
        $this->setCollection($collection);
        return parent::_prepareCollection();
    }

    /**
     * {@inheritdoc}
     */
    protected function _prepareColumns()
    {
        $this->addColumn(
            'entity_id',
            [
                'header' => __('Id'),
                'index' => 'entity_id'
            ]
        );
        $this->addColumn(
            'name',
            [
                'header' => __('Name'),
                'index' => 'name',
            ]
        );
        $this->addColumn(
            'email',
            [
                'header' => __('Email'),
                'index' => 'email',
            ]
        );
        $this->addColumn(
            'timestamp',
            [
                'header' => __('Spin Time'),
                'index' => 'timestamp',
            ]
        );
        $this->addColumn(
            'result',
            [
                'header' => __('Result'),
                'index' => 'result',
                'type' => 'options',
                'options' => [
                    0 => __('Lose'),
                    1 => __('Win'),
                ],

            ]
        );
        $this->addColumn(
            'status',
            [
                'header' => __('Status'),
                'index' => 'status',
                'type' => 'options',
                'options' => [
                    0 => __('N/A'),
                    1 => __('To Redeem'),
                    2 => __('Redeemed'),
                ],

            ]
        );
        $this->addColumn(
            'coupon',
            [
                'header' => __('Coupon'),
                'index' => 'coupon',
            ]
        );
        return parent::_prepareColumns();
    }

    public function getTotalSpins()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId);
        return $collection->getSize();
    }

    public function getTotalWins()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('result', 1);
        return $collection->getSize();
    }

    public function getTotalOrders()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $collection = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('order_id', ['neq' => 'NULL']);
        return $collection->getSize();
    }

    public function getTotalSales()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $amounts = $this->reportsFactory->create()
                                ->getCollection()
                                ->addFieldToFilter('spin_id', $spinId)
                                ->addFieldToFilter('order_amount', ['neq' => 'NULL'])
                                ->getColumnValues('order_amount');
        $total = 0;
        foreach ($amounts as $amount) {
            $total += $amount;
        }
        return $this->pricing->currency($total, true, false);
    }

    public function getGridUrl()
    {
        return $this->getUrl('spintowin/report/gridreset', ['_current' => true]);
    }
    
    public function getRowUrl($item)
    {
        return false;
    }
}
