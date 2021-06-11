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



namespace Mirasvit\Gdpr\DataManagement;

use Magento\Customer\Api\Data\CustomerInterface;
use Mirasvit\Gdpr\Api\Data\RequestInterface;

interface RequestInstanceInterface
{
    /**
     * @param CustomerInterface $customer
     *
     * @return void
     * @throws \Exception
     */
    public function validate(CustomerInterface $customer);

    /**
     * @param CustomerInterface $customer
     *
     * @return RequestInterface
     */
    public function create(CustomerInterface $customer);

    /**
     * @param RequestInterface $request
     *
     * @return bool
     */
    public function canProcess(RequestInterface $request);

    /**
     * @param RequestInterface $request
     *
     * @return mixed
     */
    public function process(RequestInterface $request);

    /**
     * @param RequestInterface $request
     *
     * @return void
     */
    public function deny(RequestInterface $request);
}
