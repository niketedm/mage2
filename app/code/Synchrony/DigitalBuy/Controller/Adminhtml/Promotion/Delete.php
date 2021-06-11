<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml\Promotion;

/**
 * Promotion delete controller
 */
class Delete extends \Synchrony\DigitalBuy\Controller\Adminhtml\Promotion
{
    /**
     * @var \Synchrony\DigitalBuy\Model\RuleFactory
     */
    protected $ruleFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param  \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
    ) {
        parent::__construct($context);
        $this->ruleFactory = $rulefactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        if ($id) {
            try {
                $model = $this->ruleFactory->create();
                $model->load($id);
                $model->delete();
                $this->messageManager->addSuccess(__('You deleted the promotion.'));
                $this->_redirect('synchrony_digitalbuy/*/');
                return;
            } catch (\Magento\Framework\Exception\LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
            } catch (\Exception $e) {
                $this->messageManager->addError(
                    __('We can\'t promotion the rule right now. Please review the log and try again.')
                );
                $this->_objectManager->get(\Psr\Log\LoggerInterface::class)->critical($e);
                $this->_redirect('synchrony_digitalbuy/*/edit', ['id' => $this->getRequest()->getParam('id')]);
                return;
            }
        }
        $this->messageManager->addError(__('We can\'t find the promotion to delete.'));
        $this->_redirect('synchrony_digitalbuy/*/');
    }
}
