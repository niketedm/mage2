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
use Webkul\SpinToWin\Api\Data\VisibilityInterface;
use Magento\Framework\DataObject\IdentityInterface;

/**
 * SpinToWin Visibility Model.
 *
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin _getResource()
 * @method \Webkul\SpinToWin\Model\ResourceModel\SpinToWin getResource()
 */
class Visibility extends AbstractModel implements VisibilityInterface, IdentityInterface
{
    /**
     * No route page id.
     */
    const NOROUTE_ENTITY_ID = 'no-route';

    /**
     * Marketplace SpinToWin cache tag.
     */
    const CACHE_TAG = 'spintowin_visibility';

    /**
     * @var string
     */
    public $_cacheTag = 'spintowin_visibility';

    /**
     * Prefix of model events names.
     *
     * @var string
     */
    public $_eventPrefix = 'spintowin_visibility';

    /**
     * Initialize resource model.
     */
    public function _construct()
    {
        $this->_init('Webkul\SpinToWin\Model\ResourceModel\Visibility');
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
     * @return \Webkul\SpinToWin\Model\Visibility
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
     * @return \Webkul\SpinToWin\Api\Data\VisibilityInterface
     */
    public function setId($id)
    {
        return $this->setData(self::ENTITY_ID, $id);
    }
}
