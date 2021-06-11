<?php
namespace Mancini\ShippingZone\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\ResultFactory;
use Magento\Ui\Component\MassAction\Filter;
use Mancini\ShippingZone\Model\ResourceModel\ShippingZone\CollectionFactory as ShippingZoneCollectionFactory;
use Mancini\ShippingZone\Model\ResourceModel\ShippingZone\Zipcodes\CollectionFactory as ZipcodesCollectionFactory;

class MassDelete extends Action
{
    /**
     * Massactions filter
     *
     * @var Filter
     */
    protected $filter;

    /** @var ShippingZoneCollectionFactory */
    protected $shippingZoneCollectionFactory;

    /** @var ZipcodesCollectionFactory */
    protected $zipcodesCollectionFactory;

    /**
     * @param Context $context
     * @param Filter $filter
     * @param ShippingZoneCollectionFactory $shippingZoneCollectionFactory
     * @param ZipcodesCollectionFactory $zipcodesCollectionFactory
     */
    public function __construct(
        Context $context,
        Filter $filter,
        ShippingZoneCollectionFactory $shippingZoneCollectionFactory,
        ZipcodesCollectionFactory $zipcodesCollectionFactory
    ) {
        $this->filter = $filter;
        $this->shippingZoneCollectionFactory = $shippingZoneCollectionFactory;
        $this->zipcodesCollectionFactory = $zipcodesCollectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $zoneIds = $this->getRequest()->getParam('zones');
        $collection = $this->shippingZoneCollectionFactory->create()->addFieldToFilter('id', array('IN' => $zoneIds));
        $zoneDeleted = 0;
        foreach ($collection->getItems() as $item) {
            $item->delete();
            $zoneDeleted++;
        }
        $this->messageManager->addSuccess(
            __('A total of %1 record(s) have been deleted.', $zoneDeleted)
        );

        return $this->resultFactory->create(ResultFactory::TYPE_REDIRECT)->setPath('shippingzone/index/index');
    }
}
