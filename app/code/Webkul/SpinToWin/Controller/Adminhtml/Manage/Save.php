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

class Save extends Action
{
    /**
     * @param Context       $context
     * @param PageFactory   $resultPageFactory
     */
    public function __construct(
        Context $context,
        \Magento\Framework\Registry $coreRegistry,
        \Webkul\SpinToWin\Model\InfoFactory $infoFactory,
        \Webkul\SpinToWin\Model\EditFormFactory $editFormFactory,
        \Webkul\SpinToWin\Model\ResultFormFactory $resultFormFactory,
        \Webkul\SpinToWin\Model\WheelFactory $wheelFactory,
        \Webkul\SpinToWin\Model\LayoutFactory $layoutFactory,
        \Webkul\SpinToWin\Model\VisibilityFactory $visibilityFactory,
        \Webkul\SpinToWin\Model\ButtonFactory $buttonFactory,
        \Webkul\SpinToWin\Model\CouponFactory $couponFactory,
        \Magento\Framework\Json\Helper\Data $jsonData,
        \Webkul\SpinToWin\Logger\Logger $logger
    ) {
        $this->coreRegistry = $coreRegistry;
        $this->logger = $logger;
        $this->infoFactory = $infoFactory;
        $this->editFormFactory = $editFormFactory;
        $this->resultFormFactory = $resultFormFactory;
        $this->wheelFactory = $wheelFactory;
        $this->layoutFactory = $layoutFactory;
        $this->visibilityFactory = $visibilityFactory;
        $this->buttonFactory = $buttonFactory;
        $this->couponFactory = $couponFactory;
        $this->jsonData = $jsonData;
        parent::__construct($context);
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $msg = "";
        if ($data && !empty($data)) {
            try {
                if ($data['scheduled']) {
                    if (strtotime($data['start_date'])<0 || strtotime($data['end_date'])<0) {
                        $msg = __("Date must be valid.");
                    }
                    if (strtotime($data['start_date']) >= strtotime($data['end_date'])) {
                        $msg = __("Start Date must be less than End Date.");
                    }
                } else {
                    $data['start_date'] = null;
                    $data['end_date'] = null;
                }
                if (!$msg) {
                    $newSave = false;
                    if (!$data['entity_id']) {
                        unset($data['entity_id']);
                        $newSave = true;
                    }
                    if (is_array($data['website_ids'])) {
                        $data['website_ids'] = implode(',', $data['website_ids']);
                    }
                    $infoModel = $this->infoFactory->create();
                    $infoModel->setData($data);
                    $infoModel->save();
                    $id = $infoModel->getId();
                    if ($newSave) {
                        $this->setDefaultData($id);
                    }
                    if (isset($data['entity_id']) && $data['entity_id']) {
                        $this->getResponse()->setHeader('Content-type', 'application/javascript');
                        $this->getResponse()->setBody($this->jsonData
                            ->jsonEncode(
                                [
                                    'success' => 1,
                                    'message' => __('Spin to Win info successfully saved.'),
                                ]
                            ));
                        return;
                    } else {
                        $this->_redirect('spintowin/*/edit', ['id' => $id]);
                        $this->messageManager->addSuccess(
                            __('Spin Campaign data has been saved successfully.')
                        );
                        return;
                    }
                }
                if (isset($data['entity_id']) && $data['entity_id']) {
                    $this->getResponse()->setHeader('Content-type', 'application/javascript');
                    $this->getResponse()->setBody($this->jsonData
                        ->jsonEncode(
                            [
                                'success' => 0,
                                'message' => $msg,
                            ]
                        ));
                    return;
                } else {
                    $this->messageManager->addError($msg);
                    $this->_redirect('spintowin/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
                    return;
                }
            } catch (\Exception $e) {
                $this->logger->info($e->getMessage());
                if (isset($data['entity_id']) && $data['entity_id']) {
                    $this->getResponse()->setHeader('Content-type', 'application/javascript');
                    $this->getResponse()->setBody($this->jsonData
                        ->jsonEncode(
                            [
                                'success' => 0,
                                'message' => __('Something went wrong while saving the campaign data. Please review the error log.'),
                            ]
                        ));
                    return;
                } else {
                    $this->messageManager->addError(
                        __('Something went wrong while saving the campaign data. Please review the error log.')
                    );
                    $this->_redirect('spintowin/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
                    return;
                }
            }
            if (isset($data['entity_id']) && $data['entity_id']) {
                $this->getResponse()->setHeader('Content-type', 'application/javascript');
                $this->getResponse()->setBody($this->jsonData
                    ->jsonEncode(
                        [
                            'success' => 0,
                            'message' => __('Something went wrong while saving the campaign data. Please review the error log.'),
                        ]
                    ));
                return;
            } else {
                $this->_redirect('spintowin/*/edit', ['id' => $this->getRequest()->getParam('entity_id')]);
                return ;
            }
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
    
    public function setDefaultData($id)
    {
        $this->editFormFactory->create()->setSpinId($id)->save();
        $this->resultFormFactory->create()->setSpinId($id)->save();
        $this->wheelFactory->create()->setSpinId($id)->save();
        $this->layoutFactory->create()->setSpinId($id)->save();
        $this->visibilityFactory->create()->setSpinId($id)->save();
        $this->buttonFactory->create()->setSpinId($id)->save();
        $this->couponFactory->create()->setSpinId($id)->save();
    }
}
