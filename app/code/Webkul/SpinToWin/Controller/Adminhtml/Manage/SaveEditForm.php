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

class SaveEditForm extends Action
{
    public function __construct(
        Context $context,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Helper\Data $helper,
        \Webkul\SpinToWin\Model\EditFormFactory $editFormFactory
    ) {
        parent::__construct($context);
        $this->_jsonData = $jsonData;
        $this->editFormFactory = $editFormFactory;
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
                if (strpos($data['logo'], '.tmp') !== false) {
                    $data['logo'] = rtrim($data['logo'], ".tmp");
                    $newFile = $this->helper->saveFile($data['logo']);
                    $data['logo'] = 'spintowin'.$newFile;
                }

                $editForm = $this->editFormFactory->create();
                $editForm->load($data['entity_id']);
                $editForm->setData($data);
                $editForm->save();

                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->_jsonData
                    ->jsonEncode(
                        [
                            'success' => 1,
                            'message' => __('Welcome form data successfully saved.'),
                            'data' => $editForm->getData()
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
