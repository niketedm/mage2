<?php
/**
 * Copyright © Magefan (support@magefan.com). All rights reserved.
 * Please visit Magefan.com for license details (https://magefan.com/end-user-license-agreement).
 */
namespace Magefan\BlogPlus\Ui\DataProvider\Product\Form\Modifier;

use Magento\Catalog\Ui\DataProvider\Product\Form\Modifier\AbstractModifier;
use Magento\Framework\Phrase;
use Magento\Ui\Component\DynamicRows;
use Magento\Ui\Component\Form\Element\DataType\Text;
use Magento\Ui\Component\Form\Element\Input;
use Magento\Ui\Component\Form\Element\Select;
use Magento\Ui\Component\Form\Field;
use Magento\Ui\Component\Form\Fieldset;
use Magento\Catalog\Model\Locator\LocatorInterface;
use Magefan\Blog\Model\ResourceModel\Post\CollectionFactory as PostCollectionFactory;
use Magento\Ui\Component\Modal;
use Magento\Framework\UrlInterface;
use Magefan\BlogPlus\Model\Config\Source\NoYes;
use Magento\Config\Model\Config\Source\Yesno;

class RelatedPosts extends AbstractModifier
{
    const DATA_SCOPE = '';
    const DATA_SCOPE_RELATED_POSTS = 'blog_related';
    const GROUP_RELATED_POSTS = 'blog_related';

    /**
     * @var string
     */
    private static $previousGroup = 'related';

    /**
     * @var int
     */
    private static $sortOrder = 95;

    /**
     * @var LocatorInterface
     */
    private $locator;

    /**
     * @var PostCollectionFactory
     */
    private $postCollectionFactory;

    /**
     * @var UrlInterface
     */
    private $urlBuilder;

    /**
     * @var string
     */
    private $scopeName;

    /**
     * @var string
     */
    private $scopePrefix;

    /**
     * @var NoYes
     */
    private $noYes;

    /**
     * @var Yesno
     */
    private $yesno;

    /**
     * RelatedPosts constructor.
     *
     * @param LocatorInterface $locator
     * @param PostCollectionFactory $postCollectionFactory
     * @param UrlInterface $urlBuilder
     * @param NoYes $noYes
     * @param Yesno $yesno
     * @param string $scopeName
     * @param string $scopePrefix
     */
    public function __construct(
        LocatorInterface $locator,
        PostCollectionFactory $postCollectionFactory,
        UrlInterface $urlBuilder,
        NoYes $noYes,
        Yesno $yesno,
        $scopeName = '',
        $scopePrefix = ''
    ) {
        $this->locator = $locator;
        $this->postCollectionFactory = $postCollectionFactory;
        $this->urlBuilder = $urlBuilder;
        $this->scopeName = $scopeName;
        $this->scopePrefix = $scopePrefix;
        $this->noYes = $noYes;
        $this->yesno = $yesno;
    }

    /**
     * @param array $meta
     * @return array
     */
    public function modifyMeta(array $meta)
    {
        $meta = array_replace_recursive(
            $meta,
            [
                static::GROUP_RELATED_POSTS => [
                    'children' => [
                        $this->scopePrefix . static::DATA_SCOPE_RELATED_POSTS => $this->getRelatedPostsFieldset(),
                    ],
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'label' => __('Related Blog Posts (Blog+)'),
                                'collapsible' => true,
                                'componentType' => Fieldset::NAME,
                                'dataScope' => static::DATA_SCOPE,
                                'sortOrder' =>
                                    $this->getNextGroupSortOrder(
                                        $meta,
                                        self::$previousGroup,
                                        self::$sortOrder
                                    ),
                            ],
                        ],

                    ],
                ],
            ]
        );

        return $meta;
    }

    /**
     * @param array $data
     * @return array
     */
    public function modifyData(array $data)
    {
        /** @var \Magento\Catalog\Model\Product $product */
        $product = $this->locator->getProduct();
        $productId = $product->getId();

        if (!$productId) {
            return $data;
        }

        $data[$productId]['links'][static::DATA_SCOPE_RELATED_POSTS] = [];

        $joinConditions = 'main_table.post_id = rp.post_id';

        $collection = $this->postCollectionFactory->create();

        $select = $collection->getSelect()
            ->joinLeft(
                ['rp' => $collection->getTable('magefan_blog_post_relatedproduct')],
                $joinConditions,
                [
                    'position' => 'rp.position',
                    'display_on_product' => 'rp.display_on_product',
                    'display_on_post' => 'rp.display_on_post',
                    'auto_related' => 'rp.auto_related',
                    'related_by_rule' => 'rp.related_by_rule'
                ]
            )
            ->where('rp.related_id = ?', $productId);

        $relatedPosts = $collection->getConnection()->fetchAll($select);

        foreach ($relatedPosts as $relatedPost) {
            $data[$productId]['links'][static::DATA_SCOPE_RELATED_POSTS][] = $this->fillData($relatedPost);
        }

        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_product_id'] = $productId;
        $data[$productId][self::DATA_SOURCE_DEFAULT]['current_store_id'] = $this->locator->getStore()->getId();

        return $data;
    }

    /**
     * Prepare data column
     *
     * @param $relatedPost
     * @return array
     */
    protected function fillData($relatedPost)
    {
        return [
            'id' => $relatedPost['post_id'],
            'title' => $relatedPost['title'],
            'position' => $relatedPost['position'],
            'display_on_product' => $relatedPost['display_on_product'],
            'display_on_post' => $relatedPost['display_on_post'],
            'auto_related' => $relatedPost['auto_related'],
            'related_by_rule' => $relatedPost['related_by_rule']
        ];
    }

    /**
     * Prepares config for the Related posts fieldset
     *
     * @return array
     */
    protected function getRelatedPostsFieldset()
    {
        $content = __(
            'Related blog posts are shown to customers in addition to the item the customer is looking at.'
        );

        return [
            'children' => [
                'button_set' => $this->getButtonSet(
                    $content,
                    __('Add Related Posts'),
                    $this->scopePrefix . static::DATA_SCOPE_RELATED_POSTS
                ),
                'modal' => $this->getGenericModal(
                    __('Add Related Posts'),
                    $this->scopePrefix . static::DATA_SCOPE_RELATED_POSTS
                ),
                static::DATA_SCOPE_RELATED_POSTS => $this->getGrid($this->scopePrefix . static::DATA_SCOPE_RELATED_POSTS),
            ],
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__fieldset-section',
                        'label' => __('Related Posts'),
                        'collapsible' => false,
                        'componentType' => Fieldset::NAME,
                        'dataScope' => '',
                        'sortOrder' => 10,
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve button set
     *
     * @param Phrase $content
     * @param Phrase $buttonTitle
     * @param string $scope
     * @return array
     */
    protected function getButtonSet(Phrase $content, Phrase $buttonTitle, $scope)
    {
        $modalTarget = $this->scopeName . '.' . static::GROUP_RELATED_POSTS . '.' . $scope . '.modal';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'formElement' => 'container',
                        'componentType' => 'container',
                        'label' => false,
                        'content' => $content,
                        'template' => 'ui/form/components/complex',
                    ],
                ],
            ],
            'children' => [
                'button_' . $scope => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'formElement' => 'container',
                                'componentType' => 'container',
                                'component' => 'Magento_Ui/js/form/components/button',
                                'actions' => [
                                    [
                                        'targetName' => $modalTarget,
                                        'actionName' => 'toggleModal',
                                    ],
                                    [
                                        'targetName' => $modalTarget . '.' . $scope . '_post_listing',
                                        'actionName' => 'render',
                                    ]
                                ],
                                'title' => $buttonTitle,
                                'provider' => null,
                            ],
                        ],
                    ],

                ],
            ],
        ];
    }

    /**
     * Prepares config for modal slide-out panel
     *
     * @param Phrase $title
     * @param string $scope
     * @return array
     */
    protected function getGenericModal(Phrase $title, $scope)
    {
        $listingTarget = $scope . '_post_listing';

        $modal = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Modal::NAME,
                        'dataScope' => '',
                        'options' => [
                            'title' => $title,
                            'buttons' => [
                                [
                                    'text' => __('Cancel'),
                                    'actions' => [
                                        'closeModal'
                                    ]
                                ],
                                [
                                    'text' => __('Add Selected Posts'),
                                    'class' => 'action-primary',
                                    'actions' => [
                                        [
                                            'targetName' => 'index = ' . $listingTarget,
                                            'actionName' => 'save'
                                        ],
                                        'closeModal'
                                    ]
                                ],
                            ],
                        ],
                    ],
                ],
            ],
            'children' => [
                $listingTarget => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'autoRender' => false,
                                'componentType' => 'insertListing',
                                'dataScope' => $listingTarget,
                                'externalProvider' => $listingTarget . '.' . $listingTarget . '_data_source',
                                'selectionsProvider' => $listingTarget . '.' . $listingTarget . '.post_columns.ids',
                                'ns' => $listingTarget,
                                'render_url' => $this->urlBuilder->getUrl('mui/index/render'),
                                'realTimeLink' => true,
                                'dataLinks' => [
                                    'imports' => false,
                                    'exports' => true
                                ],
                                'behaviourType' => 'simple',
                                'externalFilterMode' => true,
                                'imports' => [
                                    'productId' => '${ $.provider }:data.product.current_product_id',
                                    'storeId' => '${ $.provider }:data.product.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ],
                                'exports' => [
                                    'productId' => '${ $.externalProvider }:params.current_product_id',
                                    'storeId' => '${ $.externalProvider }:params.current_store_id',
                                    '__disableTmpl' => ['productId' => false, 'storeId' => false],
                                ]
                            ],
                        ],
                    ],
                ],
            ],
        ];

        return $modal;
    }

    /**
     * Retrieve grid
     *
     * @param string $scope
     * @return array
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    protected function getGrid($scope)
    {
        $dataProvider = $scope . '_post_listing';

        return [
            'arguments' => [
                'data' => [
                    'config' => [
                        'additionalClasses' => 'admin__field-wide',
                        'componentType' => DynamicRows::NAME,
                        'label' => null,
                        'columnsHeader' => false,
                        'columnsHeaderAfterRender' => true,
                        'renderDefaultRecord' => false,
                        'template' => 'ui/dynamic-rows/templates/grid',
                        'component' => 'Magento_Ui/js/dynamic-rows/dynamic-rows-grid',
                        'addButton' => false,
                        'recordTemplate' => 'record',
                        'dataScope' => 'data.links',
                        'deleteButtonLabel' => __('Remove'),
                        'dataProvider' => $dataProvider,
                        'map' => [
                            'id' => 'post_id',
                            'title' => 'title'
                        ],
                        'links' => [
                            'insertData' => '${ $.provider }:${ $.dataProvider }',
                            '__disableTmpl' => ['insertData' => false],
                        ],
                        'sortOrder' => 2,
                    ],
                ],
            ],
            'children' => [
                'record' => [
                    'arguments' => [
                        'data' => [
                            'config' => [
                                'componentType' => 'container',
                                'isTemplate' => true,
                                'is_collection' => true,
                                'component' => 'Magento_Ui/js/dynamic-rows/record',
                                'dataScope' => '',
                            ],
                        ],
                    ],
                    'children' => $this->fillMeta(),
                ],
            ],
        ];
    }

    /**
     * Retrieve meta column
     *
     * @return array
     */
    protected function fillMeta()
    {
        return [
            'id' => $this->getTextColumn('id', false, __('ID'), 0),
            'name' => $this->getTextColumn('title', false, __('Title'), 20),
            'display_on_product' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->noYes->toOptionArray(),
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' => __('Display Post On Product Page'),
                            'formElement' => Select::NAME,
                            'dataScope' => 'display_on_product',
                            'fit' => true,
                            'default' => '0',
                            'sortOrder' => 30,
                        ],
                    ],
                ]
            ],
            'display_on_post' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->noYes->toOptionArray(),
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' => __('Display Product On Post Page'),
                            'formElement' => Select::NAME,
                            'dataScope' => 'display_on_post',
                            'fit' => true,
                            'default' => '0',
                            'sortOrder' => 40,
                        ],
                    ],
                ]
            ],
            'auto_related' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->yesno->toOptionArray(),
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' => __('Auto Related'),
                            'formElement' => Select::NAME,
                            'dataScope' => 'auto_related',
                            'fit' => true,
                            'default' => '0',
                            'sortOrder' => 50,
                        ],
                    ],
                ]
            ],
            'related_by_rule' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'options' => $this->yesno->toOptionArray(),
                            'componentType' => Field::NAME,
                            'dataType' => Text::NAME,
                            'label' => __('Related By Rule'),
                            'formElement' => Select::NAME,
                            'dataScope' => 'related_by_rule',
                            'fit' => true,
                            'default' => '0',
                            'sortOrder' => 60,
                        ],
                    ],
                ]
            ],
            'actionDelete' => [
                'arguments' => [
                    'data' => [
                        'config' => [
                            'additionalClasses' => 'data-grid-actions-cell',
                            'componentType' => 'actionDelete',
                            'dataType' => Text::NAME,
                            'label' => __('Actions'),
                            'sortOrder' => 70,
                            'fit' => true,
                        ],
                    ],
                ],
            ]
        ];
    }

    /**
     * Retrieve text column structure
     *
     * @param string $dataScope
     * @param bool $fit
     * @param Phrase $label
     * @param int $sortOrder
     * @return array
     */
    protected function getTextColumn($dataScope, $fit, Phrase $label, $sortOrder)
    {
        $column = [
            'arguments' => [
                'data' => [
                    'config' => [
                        'componentType' => Field::NAME,
                        'formElement' => Input::NAME,
                        'elementTmpl' => 'ui/dynamic-rows/cells/text',
                        'component' => 'Magento_Ui/js/form/element/text',
                        'dataType' => Text::NAME,
                        'dataScope' => $dataScope,
                        'fit' => $fit,
                        'label' => $label,
                        'sortOrder' => $sortOrder,
                    ],
                ],
            ],
        ];

        return $column;
    }
}
