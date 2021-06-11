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
 * @package   mirasvit/module-sorting
 * @version   1.1.1
 * @copyright Copyright (C) 2021 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);
namespace Mirasvit\Sorting\Setup;

use Magento\Framework\Setup\InstallDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Mirasvit\Sorting\Service\SampleService;
use Mirasvit\Sorting\Repository\RankingFactorRepository;

/**
 * @codeCoverageIgnore
 */
class InstallData implements InstallDataInterface
{
    private $sampleService;

    private $rankingFactorRepository;

    public function __construct(RankingFactorRepository $rankingFactorRepository, SampleService $sampleService) {
        $this->rankingFactorRepository = $rankingFactorRepository;
        $this->sampleService           = $sampleService;
    }

    /**
     * {@inheritdoc}
     */
    public function install(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $factorTypes = ['stock', 'image'];
        foreach ($factorTypes as $type) {
            $rankingFactor = $this->rankingFactorRepository->getByType($type);
            if (!$rankingFactor->getId()) {
                $this->sampleService->addNewRankingFactor($type);
            }
        }
    }
}
