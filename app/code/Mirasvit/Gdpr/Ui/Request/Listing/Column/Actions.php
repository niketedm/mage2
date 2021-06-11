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

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\Gdpr\Api\Data\RequestInterface;

class Actions extends Column
{
    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    public function __construct(
        UrlInterface $urlBuilder,
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        array $components = [],
        array $data = []
    ) {
        $this->urlBuilder = $urlBuilder;

        parent::__construct($context, $uiComponentFactory, $components, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            foreach ($dataSource['data']['items'] as &$item) {
                $item[$this->getData('name')] = [];

                if ($item[RequestInterface::STATUS] === RequestInterface::STATUS_PENDING) {
                    $item[$this->getData('name')]['approve'] = [
                        'href'  => $this->urlBuilder->getUrl('mst_gdpr/request/approve', [
                            RequestInterface::ID => $item[RequestInterface::ID],
                        ]),
                        'label' => __('Approve'),
                    ];

                    $item[$this->getData('name')]['deny'] = [
                        'href'  => $this->urlBuilder->getUrl('mst_gdpr/request/deny', [
                            RequestInterface::ID => $item[RequestInterface::ID],
                        ]),
                        'label' => __('Deny'),
                    ];
                }
            }
        }

        return $dataSource;
    }
}
