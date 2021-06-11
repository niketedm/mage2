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

namespace Webkul\SpinToWin\Block\Adminhtml\Spin\Edit;

class Tabs extends \Magento\Backend\Block\Widget\Tabs
{
    /**
     * Constructor
     *
     * @param \Magento\Backend\Block\Template\Context $context
     * @param \Magento\Framework\Json\EncoderInterface $jsonEncoder
     * @param \Magento\Backend\Model\Auth\Session $authSession
     * @param \Magento\Framework\App\RequestInterface $request
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Template\Context $context,
        \Magento\Framework\Json\EncoderInterface $jsonEncoder,
        \Magento\Backend\Model\Auth\Session $authSession,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        parent::__construct($context, $jsonEncoder, $authSession, $data);
        $this->registry = $registry;
    }

    /**
     * @return void
     */
    protected function _construct()
    {
        parent::_construct();
        $this->setId('spin_tabs');
        $this->setDestElementId('edit_form');
        $this->setTitle(__('Spin to Win Details'));
    }

    /**
     * Prepare Layout
     *
     * @return $this
     */
    protected function _prepareLayout()
    {
        $spininfoModel = $this->registry->registry('spininfo');

        $this->addTab(
            'information',
            [
                'label' => __('Information'),
                'content' => $this->getLayout()->createBlock(
                    'Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab\Information'
                )->toHtml(),
                'active' => true
            ]
        );

        if ($spininfoModel->getId()) {
            $this->addTab(
                'form',
                [
                    'label' => __('Form'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab\Form'
                    )->toHtml()
                ]
            );
            $this->addTab(
                'wheel',
                [
                    'label' => __('Spin Wheel'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab\Wheel'
                    )->toHtml()
                ]
            );
            $this->addTab(
                'addsegment',
                [
                    'label' => __('Add Segment'),
                    'url' => $this->getUrl('spintowin/segment/edit', ['spin_id' => $spininfoModel->getId()]),
                    'class' => 'ajax'
                ]
            );
            $this->addTab(
                'segments',
                [
                    'label' => __('Segments'),
                    'url' => $this->getUrl('spintowin/segment/index', ['spin_id' => $spininfoModel->getId()]),
                    'class' => 'ajax'
                ]
            );
            $this->addTab(
                'layout',
                [
                    'label' => __('Layout'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab\Layout'
                    )->toHtml()
                ]
            );
            $this->addTab(
                'trigger',
                [
                    'label' => __('Trigger'),
                    'content' => $this->getLayout()->createBlock(
                        'Webkul\SpinToWin\Block\Adminhtml\Spin\Edit\Tab\Trigger'
                    )->toHtml()
                ]
            );
            $this->addTab(
                'report',
                [
                    'label' => __('Report'),
                    'url' => $this->getUrl('spintowin/report/index', ['spin_id' => $spininfoModel->getId()]),
                    'class' => 'ajax'
                ]
            );
        }
        
        return parent::_prepareLayout();
    }
}
