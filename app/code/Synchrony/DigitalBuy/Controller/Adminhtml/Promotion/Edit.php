<?php

namespace Synchrony\DigitalBuy\Controller\Adminhtml\Promotion;

/**
 * Promotion edit controller
 */
class Edit extends \Synchrony\DigitalBuy\Controller\Adminhtml\Promotion
{
    /**
     * @var \Magento\Framework\Registry
     */
    private $registry;

    /**
     * @var \Synchrony\DigitalBuy\Model\Rule
     */
    private $ruleFactory;

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param  \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
     */
    public function __construct(
        \Magento\Backend\App\Action\Context $context,
        \Magento\Framework\Registry $registry,
        \Synchrony\DigitalBuy\Model\RuleFactory $rulefactory
    ) {
        parent::__construct($context);
        $this->registry = $registry;
        $this->ruleFactory = $rulefactory;
    }

    public function execute()
    {
        $id = $this->getRequest()->getParam('id');
        $model = $this->ruleFactory->create();

        $this->registry->register(\Synchrony\DigitalBuy\Model\Rule::CURRENT_RULE_REGISTRY_KEY, $model);

        if ($id) {
            $model->load($id);
            if (!$model->getRuleId()) {
                $this->messageManager->addError(__('This rule no longer exists.'));
                $this->_redirect('synchrony_digitalbuy/*');
                return;
            }
            $model->getConditions()->setFormName('synchrony_promotion_form');
            $model->getConditions()->setJsFormObject(
                $model->getConditionsFieldSetId($model->getConditions()->getFormName())
            );
            $model->getActions()->setFormName('synchrony_promotion_form');
            $model->getActions()->setJsFormObject(
                $model->getActionsFieldSetId($model->getActions()->getFormName())
            );
        }

        // set entered data if was error when we do save
        $data = $this->_objectManager->get(\Magento\Backend\Model\Session::class)->getPageData(true);
        if (!empty($data)) {
            $model->addData($data);
        }

        $this->_initAction();

        $this->_addBreadcrumb(
            $id ? __('Edit Synchrony Promotion') : __('New Synchrony Promotion'),
            $id ? __('Edit Synchrony Prmotion') : __('New SYnchrony Promotion')
        );

        $this->_view->getPage()->getConfig()->getTitle()->prepend(
            $model->getRuleId() ? __('Synchrony Promotion: ') . $model->getName() : __('New Synchrony Promotion')
        );
        $this->_view->renderLayout();
    }
}
