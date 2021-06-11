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

namespace Webkul\SpinToWin\Block\Adminhtml\Segment;

/**
 * Adminhtml product grid block
 */
class Segments extends \Magento\Backend\Block\Widget\Grid\Extended
{

    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Backend\Helper\Data $backendHelper,
        \Webkul\SpinToWin\Model\SegmentsFactory $segmentsFactory,
        array $data = []
    ) {
        $this->segmentsFactory = $segmentsFactory;
        parent::__construct($context, $backendHelper, $data);
    }

    /**
     * {@inheritdoc}
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spintowin_segment_grid');
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
        $collection = $this->segmentsFactory->create()
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
            'type',
            [
                'header' => __('Type'),
                'index' => 'type',
                'type' => 'options',
                'options' => [
                    0 => __('Lose'),
                    1 => __('Win'),
                ],

            ]
        );
        $this->addColumn(
            'label',
            [
                'header' => __('Label'),
                'index' => 'label',
            ]
        );
        $this->addColumn(
            'heading',
            [
                'header' => __('Heading'),
                'index' => 'heading',
            ]
        );
        $this->addColumn(
            'limits',
            [
                'header' => __('Limit'),
                'index' => 'limits',
            ]
        );
        $this->addColumn(
            'availed',
            [
                'header' => __('Availed'),
                'index' => 'availed',
            ]
        );
        $this->addColumn(
            'gravity',
            [
                'header' => __('Gravity'),
                'index' => 'gravity',
            ]
        );
        $this->addColumn(
            'position',
            [
                'header' => __('Position'),
                'index' => 'position',
            ]
        );
        $this->addColumn(
            'action',
            [
                'header' => __('Action'),
                'width' => '100',
                'type' => 'action',
                'renderer' => 'Webkul\SpinToWin\Block\Adminhtml\Segment\Renderer\Action',
                'filter' => false,
                'sortable' => false,
            ]
        );

        return parent::_prepareColumns();
    }

    /**
     * get massaction
     * @return object
     */
    protected function _prepareMassaction()
    {
        $spinId = $this->getRequest()->getParam('spin_id');
        $this->setMassactionIdField('entity_id');
        $this->setChild('massaction', $this->getLayout()->createBlock($this->getMassactionBlockName()));
        $this->getMassactionBlock()->setFormFieldName('segmentEntityIds');
        $this->getMassactionBlock()->setUseSelectAll(false);
        $this->getMassactionBlock()->addItem(
            'delete',
            [
                'label' => __('Delete'),
                'url' => $this->getUrl(
                    '*/segment/massdelete',
                    [
                        'id'=>$spinId
                    ]
                ),
                'confirm' => __('Are you sure want to delete?')
            ]
        );
        return $this;
    }
}
