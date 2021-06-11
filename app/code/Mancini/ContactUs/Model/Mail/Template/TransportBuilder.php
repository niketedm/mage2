<?php

namespace Mancini\ContactUs\Model\Mail\Template;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Mail\EmailMessageInterface;

class TransportBuilder extends \Magento\Framework\Mail\Template\TransportBuilder
{
    /** @var string|null */
    protected $subject = '';

    /** @var EmailMessageInterface */
    protected $message;

    /**
     * @param string $subject
     * @return $this
     */
    public function setSubject($subject)
    {
        $this->subject = $subject;
        return $this;
    }

    public function addAttachment($fileName, $fileString, $fileType)
    {
        // todo: make this work

        return $this;
    }

    /**
     * @return $this
     * @throws LocalizedException
     */
    protected function prepareMessage()
    {
        parent::prepareMessage();

        if ($this->subject) {
            $this->message->setSubject($this->subject);
        }

        return $this;
    }
}
