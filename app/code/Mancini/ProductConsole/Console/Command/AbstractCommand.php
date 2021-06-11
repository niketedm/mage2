<?php

namespace Mancini\ProductConsole\Console\Command;

use Magento\Backend\App\Area\FrontNameResolver;
use Magento\Framework\App\ObjectManagerFactory;
use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;

/**
 * An Abstract class for import related commands.
 */
abstract class AbstractCommand extends Command
{
    /**
     * Name argument
     */
    const FILENAME_ARGUMENT = 'filename';

    /**
     * @var ObjectManagerFactory
     */
    protected $objectManagerFactory;

    /**
     * @var ObjectManagerInterface
     */
    protected $objectManager;


    /**
     * Constructor
     * @param ObjectManagerFactory $objectManagerFactory
     */
    public function __construct(ObjectManagerFactory $objectManagerFactory)
    {
        $this->objectManagerFactory = $objectManagerFactory;
        parent::__construct();
    }

    /**
     * Return import filename
     *
     * @return string $filename
     */
    protected function getImportFileName(InputInterface $input)
    {
        $filename = '';
        if ($input->getArgument(self::FILENAME_ARGUMENT)) {
            $filename = $input->getArgument(self::FILENAME_ARGUMENT);
        }
        return $filename;
    }

    /**
     * Gets initialized object manager
     *
     * @return ObjectManagerInterface
     * @throws LocalizedException
     */
    protected function getObjectManager()
    {
        if (null == $this->objectManager) {
            $area = FrontNameResolver::AREA_CODE;
            $this->objectManager = $this->objectManagerFactory->create($_SERVER);
            /** @var State $appState */
            $appState = $this->objectManager->get('Magento\Framework\App\State');
            $appState->setAreaCode($area);
            $configLoader = $this->objectManager->get('Magento\Framework\ObjectManager\ConfigLoaderInterface');
            $this->objectManager->configure($configLoader->load($area));
        }
        return $this->objectManager;
    }
}
