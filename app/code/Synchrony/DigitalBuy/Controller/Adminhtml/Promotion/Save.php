<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml\Promotion;

/**
 * Promotion save controller
 */
class Save extends \Synchrony\DigitalBuy\Controller\Adminhtml\Promotion
{
    /**
     * @var \Magento\Framework\Stdlib\DateTime\Filter\Date
     */
    private $dateFilter;

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule
     */
    private $ruleFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Stdlib\DateTime\Filter\Date
     * @param  \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Stdlib\DateTime\Filter\Date $dateFilter,
        \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
    ) {
        parent::__construct($context);
        $this->dateFilter = $dateFilter;
        $this->ruleFactory=$rulefactory;
    }

    /**
     * Save Action
     *
     * return void
     */
    public function execute()
    {
        if ($this->getRequest()->getPostValue()) {
            try {
                /** @var $model \Synchrony\DigitalBuy\Model\Rule */
                $model = $this->ruleFactory->create();
                $this->_eventManager->dispatch(
                    'adminhtml_controller_synchrony_promotion_prepare_save',
                    ['request' => $this->getRequest()]
                );
                $data = $this->getRequest()->getPostValue();
                $filterValues = [];
                if ($this->getRequest()->getParam('from_date')) {
                    $filterValues['from_date'] = $this->dateFilter;
                }
                if ($this->getRequest()->getParam('to_date')) {
                    $filterValues['to_date'] = $this->dateFilter;
                }
                $inputFilter = new \Zend_Filter_Input(
                    $filterValues,
                    [],
                    $data
                );
                $data = $inputFilter->getUnescaped();
                $id = $this->getRequest()->getParam('rule_id');
                if ($id) {
                    $model->load($id);
                    if ($id != $model->getId()) {
                        throw new \Magento\Framework\Exception\LocalizedException(__('The wrong promotion is specified.'));
                    }
                }

                $session = $this->_objectManager->get(\Magento\Backend\Model\Session::class);

                $validateResult = $model->validateData(new \Magento\Framework\DataObject($data));
                if ($validateResult !== true) {
                    foreach ($validateResult as $errorMessage) {
                        $this->messageManager->addError($errorMessage);
                    }
                    $session->setPageData($data);
                    $this->_redirect('synchrony_digitalbuy/*/edit', ['id' => $model->getId()]);
                    return;
                }

                if (isset($data['rule']['conditions'])) {
                    $data['conditions'] = $data['rule']['conditions'];
                }
                if (isset($data['rule']['actions'])) {
                    $data['actions'] = $data['rule']['actions'];
                }
                unset($data['rule']);
                $model->loadPost($data);
                $session->setPageData($model->getData());
                $model->save();
                $this->messageManager->addSuccess(__('You saved the promotion.'));
                $session->setPageData(false);
                if ($this->getRequest()->getParam('back')) {
                    $this->_redirect('synchrony_digitalbuy/*/edit', ['id' => $model->getId()]);
                    return;
                }
                $this->_redirect('synchrony_digitalbuy/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                $id = (int)$this->getRequest()->getParam('rule_id');
                if (!empty($id)) {
                    $this->_redirect('synchrony_digitalbuy/*/edit', ['id' => $id]);
                } else {
                    $this->_redirect('synchrony_digitalbuy/*/new');
                }
                return;
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('Something went wrong while saving the promotion data. Please review the error log.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_objectManager->get(\Magento\Backend\Model\Session::class)->setPageData($data);
                $this->_redirect('synchrony_digitalbuy/*/edit', ['id' => $this->getRequest()->getParam('rule_id')]);
                return;
            }
        }
        $this->_redirect('synchrony_digitalbuy/*/');
    }
}
