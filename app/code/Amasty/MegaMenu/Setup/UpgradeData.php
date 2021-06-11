<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


namespace Amasty\MegaMenu\Setup;

use Amasty\Base\Helper\Deploy;
use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;

class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var Deploy
     */
    private $pubDeployer;

    public function __construct(
        Deploy $pubDeployer
    ) {
        $this->pubDeployer = $pubDeployer;
    }

    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ): void {
        $setup->startSetup();

        if (version_compare($context->getVersion(), '1.9.0', '<')) {
            $pubPath = __DIR__ . '/../pub';
            $this->pubDeployer->deployFolder($pubPath);
        }

        $setup->endSetup();
    }
}
