<?php

namespace Mancini\ContactUs\Model\Mail;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Store\Model\StoreManagerInterface;

class ContactSend {
    /**
     * @var \Magento\Framework\App\Config\ScopeConfigInterface
     */
    protected $scopeConfig;

    const XML_PATH_GENERAL_CONTACT_EMAIL_TEMPLATE = 'contact/email/email_template';

    /**
     * identity email config path
     */
    const XML_PATH_GENERAL_IDENTITY = 'contact/email/sender_email_identity';

    /**
     * Recipient email config path
     */
    const XML_PATH_GENERAL_NOTIFICATION_SALES_EMAIL = 'contact/email/sales_emails';

    /**
     * Recipient email config path
     */
    const XML_PATH_GENERAL_NOTIFICATION_CUSTOMER_SERVICE_EMAIL = 'contact/email/customer_service_emails';

    /**
     * Recipient email config path
     */
    const XML_PATH_GENERAL_NOTIFICATION_WEBSITE_QUESTION_EMAIL = 'contact/email/website_question_emails';

    /**
     *
     * @var Magento\Store\Model\StoreManagerInterface
     */
    protected $storeManager;

    public function __construct(
        \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig,
        TransportBuilder $transportBuilder,
        StoreManagerInterface $storeManager,
        array $data = []
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
    }

    /**
     * Get store identifier
     *
     * @return  int
     */
    public function getStoreId()
    {
        return $this->storeManager->getStore()->getId();
    }



    public function getEmailTemplate()
    {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_CONTACT_EMAIL_TEMPLATE, $storeScope);
        if (!empty($data)) {
            return $data;
        }
        return false;
    }

    /**
     * Sample function returning config value
     * */
    public function getNotificationEmail($postData) {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = '';
        switch ($postData['department']) {
            case "1":
                $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_NOTIFICATION_SALES_EMAIL, $storeScope);
                break;
            case "2":
                $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_NOTIFICATION_CUSTOMER_SERVICE_EMAIL, $storeScope);
                break;
            case "3":
                $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_NOTIFICATION_WEBSITE_QUESTION_EMAIL, $storeScope);
                break;
            default:
                $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_NOTIFICATION_SALES_EMAIL, $storeScope);

        }

        if (!empty($data)) {
            return explode(',', $data);
        }
        return false;
    }

    public function getTransactionIdentity() {
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = $this->scopeConfig->getValue(self::XML_PATH_GENERAL_IDENTITY, $storeScope);
        if (!empty($data)) {
            return $data;
        }
        return false;
    }

    public function getTransactionIdentityEmail() {
        $transactionIdentity = $this->getTransactionIdentity();
        $xml_path = 'trans_email/ident_' . $transactionIdentity . '/email';
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = $this->scopeConfig->getValue($xml_path, $storeScope);
        if (!empty($data)) {
            return $data;
        }
        return false;
    }

    public function getTransactionIdentityName() {
        $transactionIdentity = $this->getTransactionIdentity();
        $xml_path = 'trans_email/ident_' . $transactionIdentity . '/name';
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $data = $this->scopeConfig->getValue($xml_path, $storeScope);
        if (!empty($data)) {
            return $data;
        }
        return false;
    }

    public function execute($data) {
        $postData = $data;
        /*$imagepath = '';
        $mediaUrl = $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA);


        if (!empty($data['upload_images'])) {
            $imagepath = '<img src="'.$mediaUrl.'share_your_project'.$data['upload_images'].'"/>';
        }*/

        $formatedData = [
            // 'upload_images' => $imagepath
        ];

        $report = array_merge($postData, $formatedData);

        $postObject = new \Magento\Framework\DataObject();
        $postObject->setData($report);


        $transactionNotificationEmailsTo = $this->getNotificationEmail($data);

        if (!empty($transactionNotificationEmailsTo) && count($transactionNotificationEmailsTo) > 0) {
            foreach ($transactionNotificationEmailsTo as $email) {
                $this->transportBuilder->addTo($email);
            }
        }

        try {

            $transport = $this->transportBuilder
                ->setTemplateIdentifier($this->getEmailTemplate())
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_FRONTEND, 'store' => $this->getStoreId()])
                ->setTemplateVars(['data' => $postObject])
                ->setFrom(['name' => $this->getTransactionIdentityName(), 'email' => $this->getTransactionIdentityEmail()])
                ->getTransport();

            $transport->sendMessage();
            return true;
        } catch (\Exception $e) {
            //print_r($e->getMessage());
            return false;
        }
    }


}
