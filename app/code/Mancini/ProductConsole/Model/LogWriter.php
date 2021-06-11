<?php
namespace Mancini\ProductConsole\Model;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\Framework\Mail\Message;
use Magento\Store\Model\StoreManagerInterface;
use Mancini\ProductConsole\Helper\Data;

class LogWriter
{
    protected $directoryWrite;
    protected $filesystem;
    protected $storeManager;
    protected $_filename = 'product_daily';
    protected $_suffix = '';
    protected $mailMessage;
    protected $helper;

    public function __construct(
        Filesystem $filesystem,
        Message $mailMessage,
        Data $helper,
        StoreManagerInterface $storeManager
    )
    {
        $this->directoryWrite = $filesystem->getDirectoryWrite(DirectoryList::LOG);
        $this->filesystem = $filesystem;
        $this->mailMessage = $mailMessage;
        $this->helper = $helper;
        $this->storeManager = $storeManager;
        $this->storeManager->setCurrentStore('admin');
    }

    public function setSuffix($suffix)
    {
        $this->_suffix = $suffix;
    }

    public function writeLog($content)
    {
        $file = $this->_filename;
        if ($this->_suffix != '') {
            $file .= '_' . $this->_suffix;
        }
        $file .= '.log';
        $content .= "\r\n";
        $this->directoryWrite->writeFile($file, $content, 'a+');
    }

    public function getLogContent()
    {
        $file = $this->_filename;
        if ($this->_suffix != '') {
            $file .= '_' . $this->_suffix;
        }
        $file .= '.log';
        try {
            $content = $this->directoryWrite->readFile($file);
            return $content;
        } catch (Exception $e) {
            echo 'Can not find log file';
            return;
        }
    }

    public function sendLogEmail()
    {
        $file = $this->_filename;
        if ($this->_suffix != '') {
            $file .= '_' . $this->_suffix;
        }
        $file .= '.log';
        try {
            $content = $this->directoryWrite->readFile($file);
        } catch (Exception $e) {
            echo 'Can not find log file';
            return;
        }
        $configEmail = $this->helper->getStoreConfigValue('product_console/email_settings/email_to');
        if (empty($configEmail)) {
            return;
        }
        $emailArray = explode(',', $configEmail);
        $emails = array();
        foreach ($emailArray as $emailAddress) {
            $emails[] = trim($emailAddress);
        }
        //$emails = array('jeffrey.wang@Mancinisoftware.com', 'grass.huang@Mancinisoftware.com');
        try {
            $email = new \Zend_Mail();
            $email->setSubject("Daily Product Import Log");
            $email->setBodyText($content);
            $email->setFrom('logerror@sleepworld.com', 'Import Log');
            $email->addTo($emails);
            $email->send();
        } catch (Exception $e) {
            echo "Email Send Error: " . $e->getMessage();
        }
    }

}

?>
