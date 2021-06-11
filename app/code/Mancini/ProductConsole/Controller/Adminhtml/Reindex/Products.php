<?php

namespace Mancini\ProductConsole\Controller\Adminhtml\Reindex;

use Exception;
use InvalidArgumentException;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Backend\Model\View\Result\RedirectFactory;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Indexer\ConfigInterface;
use Magento\Framework\Indexer\IndexerInterface;
use Magento\Framework\Indexer\StateInterface;
use Magento\Framework\Registry;
use Magento\Indexer\Model\Indexer;
use Magento\Indexer\Model\Indexer\State;
use Magento\Indexer\Model\IndexerFactory;

class Products extends Action
{
    /** @var IndexerFactory */
    protected $indexerFactory;

    /** @var ConfigInterface */
    protected $config;

    /** @var array */
    protected $sharedIndexesComplete = [];

    /**
     * Core registry
     *
     * @var Registry
     */
    protected $coreRegistry;

    /**
     * Result redirect factory
     *
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    public function __construct(
        IndexerFactory $indexerFactory,
        ConfigInterface $config,
        Registry $coreRegistry,
        RedirectFactory $resultRedirectFactory,
        Context $context
    ) {
        $this->indexerFactory = $indexerFactory;
        $this->config = $config;
        $this->coreRegistry = $coreRegistry;
        $this->resultRedirectFactory = $resultRedirectFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $indexers = $this->getIndexers();
        foreach ($indexers as $indexer) {
            try {
                $this->validateIndexerStatus($indexer);
                $startTime = microtime(true);
                $indexerConfig = $this->config->getIndexer($indexer->getId());
                $sharedIndex = $indexerConfig['shared_index'];

                // Skip indexers having shared index that was already complete
                if (!in_array($sharedIndex, $this->sharedIndexesComplete)) {
                    $indexer->reindexAll();
                    if ($sharedIndex) {
                        $this->validateSharedIndex($sharedIndex);
                    }
                }
                $resultTime = microtime(true) - $startTime;
                $this->messageManager->addSuccess(
                    $indexer->getTitle() . ' index has been rebuilt successfully in ' . gmdate('H:i:s', $resultTime)
                );
            } catch (LocalizedException $e) {
                $this->messageManager->addError($e->getMessage());
                // we must have an exit code higher than zero to indicate something was wrong
                //$returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
            } catch (Exception $e) {
                $this->messageManager->addError($indexer->getTitle() . ' indexer process unknown error:');
                $this->messageManager->addError($e->getMessage());
                // we must have an exit code higher than zero to indicate something was wrong
                //$returnValue = \Magento\Framework\Console\Cli::RETURN_FAILURE;
            }
        }

        $resultRedirect = $this->resultRedirectFactory->create();
        $resultRedirect->setPath('product_console/import/index');
        return $resultRedirect;
    }

    protected function getIndexers()
    {
        $requestedTypes = [
            'catalog_category_product',
            'catalog_product_category',
            'catalog_product_price',
            'catalog_product_attribute',
            'cataloginventory_stock',
            'catalogsearch_fulltext'
        ];
        $indexers = [];
        $unsupportedTypes = [];
        foreach ($requestedTypes as $code) {
            $indexer = $this->indexerFactory->create();
            try {
                $indexer->load($code);
                $indexers[] = $indexer;
            } catch (Exception $e) {
                $unsupportedTypes[] = $code;
            }
        }

        return $indexers;
    }

    /**
     * Validate that indexer is not locked
     *
     * @param IndexerInterface $indexer
     * @return void
     * @throws LocalizedException
     */
    private function validateIndexerStatus(IndexerInterface $indexer)
    {
        if ($indexer->getStatus() == StateInterface::STATUS_WORKING) {
            throw new LocalizedException(
                __(
                    '%1 index is locked by another reindex process. Skipping.',
                    $indexer->getTitle()
                )
            );
        }
    }

    /**
     * Validate indexers by shared index ID
     *
     * @param string $sharedIndex
     * @return $this
     * @throws Exception
     */
    private function validateSharedIndex($sharedIndex)
    {
        if (empty($sharedIndex)) {
            throw new InvalidArgumentException('sharedIndex must be a valid shared index identifier');
        }
        $indexerIds = $this->getIndexerIdsBySharedIndex($sharedIndex);
        if (empty($indexerIds)) {
            return $this;
        }
        foreach ($indexerIds as $indexerId) {
            /** @var Indexer $indexer */
            $indexer = $this->indexerFactory->create();
            $indexer->load($indexerId);
            /** @var State $state */
            $state = $indexer->getState();
            $state->setStatus(StateInterface::STATUS_VALID);
            $state->save();
        }
        $this->sharedIndexesComplete[] = $sharedIndex;
        return $this;
    }

    /**
     * Get indexer ids that have common shared index
     *
     * @param string $sharedIndex
     * @return array
     */
    private function getIndexerIdsBySharedIndex($sharedIndex)
    {
        $indexers = $this->config->getIndexers();
        $result = [];
        foreach ($indexers as $indexerConfig) {
            if ($indexerConfig['shared_index'] == $sharedIndex) {
                $result[] = $indexerConfig['indexer_id'];
            }
        }
        return $result;
    }
}
