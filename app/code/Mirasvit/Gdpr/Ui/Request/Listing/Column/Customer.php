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



namespace Mirasvit\Gdpr\Ui\Request\Listing\Column;

use Magento\Customer\Model\ResourceModel\CustomerRepository;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Gdpr\Api\Data\RequestInterface;

class Customer extends Column
{
    private $customerRepository;

    public function __construct(
        CustomerRepository $customerRepository,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->customerRepository = $customerRepository;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $label = __('Removed');
                $url   = '';

                if ($item[RequestInterface::CUSTOMER_ID]) {
                    try {
                        $customer = $this->customerRepository->getById($item[RequestInterface::CUSTOMER_ID]);
                        $label    = $customer->getFirstname() . ' ' . $customer->getLastname();
                        $url      = $this->context->getUrl('customer/index/edit', ['id' => $customer->getId()]);
                    } catch (\Exception $e) {
                    }
                }

                $item[$this->getData('name')] = [
                    'label' => $label,
                    'url'   => $url,
                ];
            }
        }

        return $dataSource;
    }
}
