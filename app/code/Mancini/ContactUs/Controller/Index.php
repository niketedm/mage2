<?php

namespace Mancini\ContactUs\Controller;

use Exception;
use Magento\Contact\Controller\Index\Post;
use Magento\Contact\Model\ConfigInterface;
use Magento\Contact\Model\MailInterface;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Area;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\DataObject;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;
use Psr\Log\LoggerInterface;
use Mancini\ContactUs\Model\Mail\Template\TransportBuilder;
use Zend_Validate;
use Zend_Validate_Exception;

class Index extends Post
{
    /** @var TransportBuilder */
    protected $transportBuilder;

    /** @var StateInterface */
    protected $inlineTranslation;

    /** @var ScopeConfigInterface */
    protected $scopeConfig;

    /** @var DataPersistorInterface */
    protected $dataPersistor;

    /** @var StoreManagerInterface */
    protected $storeManager;

    /** @var string */
    protected $redirectPath = '';

    /** @var string */
    protected $noPostPath = '';

    /** @var string */
    protected $persistorKey = '';

    /** @var string */
    protected $subject = '';

    /** @var bool */
    protected $uploadNeeded = false;

    /**
     * Index constructor.
     * @param Context $context
     * @param ConfigInterface $contactsConfig
     * @param MailInterface $mail
     * @param DataPersistorInterface $dataPersistor
     * @param TransportBuilder $transportBuilder
     * @param StateInterface $inlineTranslation
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        Context $context,
        ConfigInterface $contactsConfig,
        MailInterface $mail,
        DataPersistorInterface $dataPersistor,
        TransportBuilder $transportBuilder,
        StateInterface $inlineTranslation,
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        LoggerInterface $logger = null
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->inlineTranslation = $inlineTranslation;
        $this->scopeConfig = $scopeConfig;
        $this->dataPersistor = $dataPersistor;
        $this->storeManager = $storeManager;

        parent::__construct($context, $contactsConfig, $mail, $dataPersistor, $logger);
    }

    /**
     * @param array $post
     * @return DataObject
     * @throws Zend_Validate_Exception
     * @throws Exception
     */
    protected function validatePostData($post)
    {
        $postObject = new DataObject();
        $postObject->setData($post);

        $error = false;

        if (!Zend_Validate::is(trim($post['name']), 'NotEmpty')) {
            $error = true;
        }

        if (!Zend_Validate::is(trim($post['department']), 'EmailAddress')) {
            $error = true;
        }

        if (!Zend_Validate::is(trim($post['telephone']), 'NotEmpty')) {
            $error = true;
        }

        if (!Zend_Validate::is(trim($post['comment']), 'NotEmpty')) {
            $error = true;
        }

        if (!Zend_Validate::is(trim($post['email']), 'EmailAddress')) {
            $error = true;
        }

        if (Zend_Validate::is(trim($post['hideit']), 'NotEmpty')) {
            $error = true;
        }

        if ($error) {
            throw new Exception();
        }

        return $postObject;
    }

    /**
     * @return array|null
     */
    protected function getUploadFileData()
    {
        if (isset($_FILES['file']) && !empty($_FILES['file']['name'])) {
            return [
                'content' => file_get_contents($_FILES['file']['tmp_name']),
                'type' => $_FILES['file']['type'],
                'name' => $_FILES['file']['name']
            ];
        }

        return null;
    }

    /**
     * Post user question
     *
     * @return void
     * @throws Exception
     */
    public function execute()
    {
        $post = $this->getRequest()->getPostValue();
        if (!$post) {
            $this->_redirect($this->noPostPath);
            return;
        }

        $this->inlineTranslation->suspend();
        try {
            $postObject = $this->validatePostData($post);

            $storeScope = ScopeInterface::SCOPE_STORE;
            $this->transportBuilder
                ->setTemplateIdentifier($this->scopeConfig->getValue(self::XML_PATH_EMAIL_TEMPLATE, $storeScope))
                ->setTemplateOptions(
                    [
                        'area' => Area::AREA_FRONTEND,
                        'store' => Store::DEFAULT_STORE_ID,
                    ]
                )
                ->setTemplateVars(['data' => $postObject])
                ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_SENDER, $storeScope))
                ->addTo($post['department'])
                ->setReplyTo($post['email']);
            if ($this->subject) {
                $this->transportBuilder->setSubject($this->subject);
            }
            if ($this->uploadNeeded && $uploadFile = $this->getUploadFileData()) {
                $this->transportBuilder->addAttachment($uploadFile['name'], $uploadFile['content'], $uploadFile['type']);
            }
            $transport = $this->transportBuilder->getTransport();

            $transport->sendMessage();
            $this->inlineTranslation->resume();
            $this->messageManager->addSuccess(
                __('Thanks for contacting us with your comments and questions. We\'ll respond to you very soon.')
            );
            $this->dataPersistor->clear($this->persistorKey);
            $this->_redirect($this->redirectPath);
            return;
        } catch (Exception $e) {
            $this->inlineTranslation->resume();
            $this->messageManager->addError(
                __('We can\'t process your request right now. Sorry, that\'s all we know.')
            );
            $this->dataPersistor->set($this->persistorKey, $post);
            $this->_redirect($this->redirectPath);
            return;
        }
    }
}
