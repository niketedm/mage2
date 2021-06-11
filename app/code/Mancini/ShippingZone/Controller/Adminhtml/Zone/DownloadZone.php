<?php

namespace Mancini\ShippingZone\Controller\Adminhtml\Zone;

use Exception;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Mancini\ShippingZone\Controller\Adminhtml\AbstractAction;

class DownloadZone extends AbstractAction
{
    /**
     * @return ResponseInterface|ResultInterface
     * @throws Exception
     */
    public function execute()
    {
        /** start csv content and set template */
        $content = '"zone_name","standard_shipping_cost","premium_shipping_cost","is_delete"';
        $content .= "\n";

        $collection = $this->shippingZoneFactory->create()->getCollection();
        foreach ($collection as $zone) {
            $content .= '"' . $zone->getZoneName() . '","' . $zone->getStandardShippingCost() . '","' . $zone->getPremiumShippingCost() . '",""' . "\n";
        }
        $fileName = "shipping_zones";
        return $this->fileFactory->create($fileName . '.csv', $content, DirectoryList::VAR_DIR);
    }
}
