<?php
namespace Rootways\Authorizecim\Gateway\Validator;

use Magento\Payment\Gateway\Validator\AbstractValidator;

abstract class AbstractResponseValidator extends AbstractValidator
{
    const RESULTCODE = 'RESULTCODE';
    const RESPONSECODE = 'RESPONSECODE';
    const TRANSID = 'TRANSID';
    const AVSRESPCODE = 'AVSRESULTCODE';
    const CVV2RESPCODE = 'CVVRESULTCODE';
    const STATUSMSG = 'TEXT';
    
    protected function responseCode(array $response)
    {
        return isset($response['transactionResponse']['responseCode'])
            && ($response['transactionResponse']['responseCode'] == '1' || $response['transactionResponse']['responseCode'] == '4');
    }
    
    protected function approvalStatus(array $response)
    {
        return $response['messages']['resultCode'] == 'Ok';
    }
    
    protected function validateTxrefnum(array $response)
    {
        return isset($response['transactionResponse']['transId'])
            && $response['transactionResponse']['transId'] != '';
    }
}
