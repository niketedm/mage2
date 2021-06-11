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



namespace Mirasvit\GdprCookie\Ui\Cookie\Listing\Column;

use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Ui\Component\Listing\Columns\Column;
use Mirasvit\GdprCookie\Api\Data\CookieInterface;

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
                $item[$this->getData('name')]           = [];

                $item[$this->getData('name')]['edit'] = [
                    'href'  => $this->urlBuilder->getUrl('gdpr_cookie/cookie/edit', [
                        CookieInterface::ID => $item[CookieInterface::ID],
                    ]),
                    'label' => __('Edit'),
                ];

                $item[$this->getData('name')]['delete'] = [
                    'href'  => $this->urlBuilder->getUrl('gdpr_cookie/cookie/delete', [
                        CookieInterface::ID => $item[CookieInterface::ID],
                    ]),
                    'label' => __('Delete'),
                ];
            }
        }

        return $dataSource;
    }
}
