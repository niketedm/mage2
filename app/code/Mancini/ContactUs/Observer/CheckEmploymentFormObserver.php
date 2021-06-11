<?php

namespace Mancini\ContactUs\Observer;

use Magento\Captcha\Helper\Data;
use Magento\Captcha\Observer\CaptchaStringResolver;
use Magento\Framework\App\Action\Action;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\Response\RedirectInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;

class CheckEmploymentFormObserver implements ObserverInterface
{
    /** @var Data */
    protected $_helper;

    /** @var ActionFlag */
    protected $_actionFlag;

    /** @var ManagerInterface */
    protected $messageManager;

    /** @var RedirectInterface */
    protected $redirect;

    /** @var CaptchaStringResolver */
    protected $captchaStringResolver;

    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /**
     * @param Data $helper
     * @param ActionFlag $actionFlag
     * @param ManagerInterface $messageManager
     * @param RedirectInterface $redirect
     * @param CaptchaStringResolver $captchaStringResolver
     * @param DataPersistorInterface $dataPersistor
     */
    public function __construct(
        Data $helper,
        ActionFlag $actionFlag,
        ManagerInterface $messageManager,
        RedirectInterface $redirect,
        CaptchaStringResolver $captchaStringResolver,
        DataPersistorInterface $dataPersistor
    ) {
        $this->_helper = $helper;
        $this->_actionFlag = $actionFlag;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->captchaStringResolver = $captchaStringResolver;
        $this->dataPersistor = $dataPersistor;
    }

    /**
     * Check CAPTCHA on Contact Us page
     *
     * @param Observer $observer
     * @return void
     */
    public function execute(Observer $observer)
    {
        $formId = 'employment';
        $captcha = $this->_helper->getCaptcha($formId);
        if ($captcha->isRequired()) {
            /** @var Action $controller */
            $controller = $observer->getControllerAction();
            if (!$captcha->isCorrect($this->captchaStringResolver->resolve($controller->getRequest(), $formId))) {
                $this->messageManager->addError(__('Incorrect CAPTCHA.'));
                $this->dataPersistor->set($formId, $controller->getRequest()->getPostValue());
                $this->_actionFlag->set('', Action::FLAG_NO_DISPATCH, true);
                $this->redirect->redirect($controller->getResponse(), 'employment');
            }
        }
    }
}
