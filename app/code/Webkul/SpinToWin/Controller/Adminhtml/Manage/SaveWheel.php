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

class SaveWheel extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Model\WheelFactory $wheelFactory
    ) {
        parent::__construct($context);
        $this->_jsonData = $jsonData;
        $this->wheelFactory = $wheelFactory;
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
                if (strpos($data['center_image'], '.tmp') !== false) {
                    $data['center_image'] = rtrim($data['center_image'], ".tmp");
                    $newFile = $this->helper->saveFile($data['center_image']);
                    $data['center_image'] = 'spintowin'.$newFile;
                }
                if (strpos($data['background_image'], '.tmp') !== false) {
                    $data['background_image'] = rtrim($data['background_image'], ".tmp");
                    $newFile = $this->helper->saveFile($data['background_image']);
                    $data['background_image'] = 'spintowin'.$newFile;
                }
                if (strpos($data['pin_image'], '.tmp') !== false) {
                    $data['pin_image'] = rtrim($data['pin_image'], ".tmp");
                    $newFile = $this->helper->saveFile($data['pin_image']);
                    $data['pin_image'] = 'spintowin'.$newFile;
                }
                $wheelData = $this->wheelFactory->create();
                $wheelData->load($data['entity_id']);
                $wheelData->setData($data);
                $wheelData->save();

                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 1,
                            'message' => __('Spin wheel data successfully saved.'),
                            'data' => $wheelData->getData()
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
