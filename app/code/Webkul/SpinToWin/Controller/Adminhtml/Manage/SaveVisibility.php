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

namespace Webkul\SpinToWin\Controller\Adminhtml\Manage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;

class SaveVisibility extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Model\VisibilityFactory $visibilityFactory
    ) {
        parent::__construct($context);
        $this->_jsonData = $jsonData;
        $this->visibilityFactory = $visibilityFactory;
        $this->helper = $helper;
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function execute()
    {
        try {
            $data = $this->getRequest()->getParams();
            if (!empty($data) && isset($data['entity_id'])) {
                $visibilityData = $this->visibilityFactory->create();
                if (!$data['wheel_depended']) {
                    $data['wheel']=null;
                } else {
                    $data['wheel'] = implode(',', $data['wheel']);
                }
                if (!$data['button_depended']) {
                    $data['button']=null;
                } else {
                    $data['button'] = implode(',', $data['button']);
                }
                $data['events'] = implode('_', $data['events']);
                $visibilityData->load($data['entity_id']);
                $visibilityData->setData($data);
                $visibilityData->save();

                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 1,
                            'message' => __('Visibility data successfully saved.')
                        ]
                    ));
                return;
            } else {
                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 0,
                            'message' => __('Invalid data.')
                        ]
                    ));
                return;
            }
        } catch (\Exception $e) {
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
}
