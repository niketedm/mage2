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



namespace Mirasvit\Gdpr\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Magento\Framework\ObjectManagerInterface;
use Magento\Framework\Registry;

class CronCommand extends Command
{
    private $objectManager;

    private $registry;

    public function __construct(
        ObjectManagerInterface $objectManager,
        Registry $registry
    ) {
        $this->objectManager = $objectManager;
        $this->registry      = $registry;

        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('mirasvit:gdpr:cron')
            ->setDescription('For test purpose')
            ->setDefinition([]);

        parent::configure();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->registry->register('isSecureArea', true);

        $classes = [
            //            \Mirasvit\Gdpr\Cron\AutoRemoveCron::class,
            \Mirasvit\Gdpr\Cron\HandleRequestsCron::class,
        ];

        foreach ($classes as $class) {
            $output->write("$class...");

            $cron = $this->objectManager->create($class);
            $cron->execute();

            $output->writeln("<info>done</info>");
        }
    }
}
