<?php

namespace Synchrony\DigitalBuy\Model\Rule\Metadata;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Convert\DataObject;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Store\Model\System\Store;

/**
 * Metadata provider for Synchrony promotion rule edit form.
 */
class ValueProvider
{
    /**
     * @var Store
     */
    protected $store;

    /**
     * @var GroupRepositoryInterface
     */
    protected $groupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var DataObject
     */
    protected $objectConverter;

    /**
     * Initialize dependencies.
     *
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param DataObject $objectConverter
     * @param Store $store
     */
    public function __construct(
        GroupRepositoryInterface $groupRepository,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        DataObject $objectConverter,
        Store $store
    ) {
        $this->groupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->objectConverter = $objectConverter;
        $this->store = $store;
    }

    /**
     * Get metadata for Synchrony promotion rule form. It will be merged with form UI component declaration.
     *
     * @return array
     */
    public function getMetadataValues()
    {
        $customerGroups = $this->groupRepository->getList($this->searchCriteriaBuilder->create())->getItems();

        return [
            'rule_information' => [
                'children' => [
                    'website_ids' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->store->getWebsiteValuesForForm(),
                                ],
                            ],
                        ],
                    ],
                    'customer_group_ids' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => $this->objectConverter->toOptionArray($customerGroups, 'id', 'code'),
                                ],
                            ],
                        ],
                    ],
                    'is_active' => [
                        'arguments' => [
                            'data' => [
                                'config' => [
                                    'options' => [
                                        ['label' => __('Active'), 'value' => '1'],
                                        ['label' => __('Inactive'), 'value' => '0']
                                    ],
                                ],
                            ],
                        ],
                    ],
                ]
            ],
        ];
    }
}
