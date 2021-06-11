<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\SpinToWin\Model;

use Magento\Framework\Model\AbstractModel;
use Webkul\SpinToWin\Api\Data\SegmentsInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * SpinToWin Segments Model.
 *
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin _getResource()
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin getResource()
 */
class Segments extends AbstractModel implements SegmentsInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Marketplace SpinToWin cache tag.
     */
    const CACHE_TAG = 'spintowin_segments';

    /**
     * @var string
     */
    public $_cacheTag = 'spintowin_segments';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    public $_eventPrefix = 'spintowin_segments';

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magento\SalesRule\Model\RuleFactory $ruleFactory,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
        $this->ruleFactory = $ruleFactory;
    }

    /**
     * Initialize resource model.
     */
    public function _construct()
    {
        $this->_init('Webkul\SpinToWin\Model\ResourceModel\Segments');
    }

    /**
     * Load object data.
     *
     * @param int|null $id
     * @param string   $field
     *
     * @return $this
     */
    public function load($id, $field = null)
    {
        if ($id === null) {
            return $this->noRouteSpinToWin();
        }

        return parent::load($id, $field);
    }

    /**
     * Load No-Route SpinToWin.
     *
     * @return \Webkul\SpinToWin\Model\Segments
     */
    public function noRouteSpinToWin()
    {
        return $this->load(self::NOROUTE_ENTITY_ID, $this->getIdFieldName());
    }

    /**
     * Get identities.
     *
     * @return array
     */
    public function getIdentities()
    {
        return [self::CACHE_TAG.'_'.$this->getId()];
    }

    /**
     * Get ID.
     *
     * @return int
     */
    public function getId()
    {
        return parent::getData(self::ENTITY_ID);
    }

    /**
     * Set ID.
     *
     * @param int $id
     *
     * @return \Webkul\SpinToWin\Api\Data\SegmentsInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }

    public function getSalesRule()
    {
        $salesRule = $this->ruleFactory->create();
        if ($this->getRuleId()) {
            $salesRule->load($this->getRuleId());
        }
        return $salesRule;
    }
}
