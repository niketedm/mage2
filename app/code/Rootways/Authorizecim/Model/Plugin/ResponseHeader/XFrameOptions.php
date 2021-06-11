<?php
namespace Rootways\Authorizecim\Model\Plugin\ResponseHeader;

class XFrameOptions extends \Magento\Framework\App\Response\HeaderProvider\XFrameOptions
{
    public function __construct(
        $xFrameOpt = 'SAMEORIGIN',
        \Magento\Framework\App\Request\Http $request
    ) {
        // Skip this for the iFrame page
        if ($request->getModuleName() == 'rootways_authorizecim') {
            return;
        }
        
        return parent::__construct($xFrameOpt);
    }
}
