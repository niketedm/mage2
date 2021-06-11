<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-gdpr
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Gdpr\Controller\Customer;


class Remove extends AbstractRequest
{
    /**
     * {@inheritdoc}
     */
    public function execute()
    {
        if (!$this->validate()) {
            return $this->_redirect($this->_redirect->getRefererUrl());
        }

        $customer = $this->getCustomer();

        try {
            $this->removeRequest->validate($customer);

            $request = $this->removeRequest->create($customer);

            if ($this->removeRequest->canProcess($request)) {
                $this->removeRequest->process($request);

                $this->messageManager->addSuccessMessage('Your request was successfully processed.');
            } else {
                $this->messageManager->addSuccessMessage('Your request was successfully sent.');
            }
        } catch (\Exception $e) {
            $this->messageManager->addErrorMessage($e->getMessage());
        }

        return $this->_redirect($this->_redirect->getRefererUrl());
    }
}