<?php
namespace Rootways\Authorizecim\Gateway\Validator;

use Rootways\Authorizecim\Gateway\Validator\AbstractResponseValidator;
use Magento\Payment\Gateway\Helper\SubjectReader;

class ResponseValidator extends AbstractResponseValidator
{
    public function validate(array $validationSubject)
    {
        $response = SubjectReader::readResponse($validationSubject);
        $errorMessages = [];
        $errorCode = [];
        $validationResult = $this->responseCode($response)
            && $this->validateTxrefnum($response);

        if (!$validationResult) {
            if (isset($response['transactionResponse']['errors']['error']['errorText'])) {
                $errorCode[] = $response['transactionResponse']['errors']['error']['errorCode'];
                $errorMessages[] = __($response['transactionResponse']['errors']['error']['errorText']);
            } else {
                if (!empty($response['messages']['resultCode']) &&
                   $response['messages']['resultCode'] != 'Ok' &&
                    !empty($response['messages']['message']['text'])
                   ) {
                    $errorCode[] = $response['messages']['message']['code'];
                    $errorMessages[] = __($response['messages']['message']['text']);
                }
            }
        }
        
        return $this->createResult($validationResult, $errorMessages, $errorCode);
    }
}
