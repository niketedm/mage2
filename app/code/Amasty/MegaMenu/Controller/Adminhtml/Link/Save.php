<?php
/**
 * @author Amasty Team
 * @copyright Copyright (c) 2021 Amasty (https://www.amasty.com)
 * @package Amasty_MegaMenu
 */


declare(strict_types=1);

namespace Amasty\MegaMenu\Controller\Adminhtml\Link;

use Amasty\MegaMenu\Api\Data\Menu\ItemInterface;
use Amasty\MegaMenu\Api\Data\Menu\LinkInterface;
use Amasty\MegaMenu\Model\Menu\Link;
use Amasty\MegaMenu\Model\OptionSource\UrlKey;
use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\Exception\LocalizedException;
use Magento\Store\Model\Store;

class Save extends Action
{
    /**
     * Authorization level of a basic admin session
     *
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Amasty_MegaMenu::menu_links';

    /**
     * @var \Magento\Framework\App\Request\DataPersistorInterface
     */
    private $dataPersistor;

    /**
     * @var \Magento\Framework\DataObject
     */
    private $dataObject;

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Amasty\MegaMenu\Model\Repository\LinkRepository
     */
    private $linkRepository;

    /**
     * @var \Amasty\MegaMenu\Model\Menu\LinkFactory
     */
    private $linkFactory;

    /**
     * @var \Amasty\MegaMenu\Model\Menu\ItemFactory
     */
    private $itemFactory;

    /**
     * @var \Amasty\MegaMenu\Model\Repository\ItemRepository
     */
    private $itemRepository;

    /**
     * @var \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\GetMaxSortOrder
     */
    private $maxSortOrder;

    public function __construct(
        Action\Context $context,
        \Amasty\MegaMenu\Model\ResourceModel\Menu\Item\Position\GetMaxSortOrder $maxSortOrder,
        \Amasty\MegaMenu\Model\Repository\LinkRepository $linkRepository,
        \Amasty\MegaMenu\Model\Repository\ItemRepository $itemRepository,
        \Amasty\MegaMenu\Model\Menu\LinkFactory $linkFactory,
        \Amasty\MegaMenu\Model\Menu\ItemFactory $itemFactory,
        \Magento\Framework\App\Request\DataPersistorInterface $dataPersistor,
        \Magento\Framework\DataObject $dataObject,
        \Psr\Log\LoggerInterface $logger
    ) {
        parent::__construct($context);
        $this->dataPersistor = $dataPersistor;
        $this->dataObject = $dataObject;
        $this->logger = $logger;
        $this->linkRepository = $linkRepository;
        $this->linkFactory = $linkFactory;
        $this->itemFactory = $itemFactory;
        $this->itemRepository = $itemRepository;
        $this->maxSortOrder = $maxSortOrder;
    }

    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        $linkId = (int)$this->getRequest()->getParam('link_id');
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();
        if ($data) {
            /** @var Link $model */
            $model = $this->linkFactory->create();

            try {
                if ($linkId) {
                    $model = $this->linkRepository->getById($linkId);
                }

                /** @var \Amasty\MegaMenu\Model\Menu\Item $itemContent */
                $itemContent = $this->retrieveItemContent($data);
                $data = $this->prepareData($data);
                $model->setData($data);
                $link = $this->linkRepository->save($model);
                $sortOrder = $this->maxSortOrder->execute();
                $itemContent->setEntityId($link->getEntityId());
                $itemContent->setSortOrder($sortOrder);
                $this->itemRepository->save($itemContent);

                $this->messageManager->addSuccessMessage(__('The Custom Menu Item was successfully saved.'));
                $this->dataPersistor->clear(Link::PERSIST_NAME);

                if ($this->getRequest()->getParam('back')) {
                    $store = (int) $this->_request->getParam('store_id', Store::DEFAULT_STORE_ID);

                    return $resultRedirect->setPath(
                        '*/*/edit',
                        [
                            'id' => $model->getEntityId(),
                            'store' => $store
                        ]
                    );
                }
            } catch (LocalizedException $e) {
                $this->messageManager->addErrorMessage($e->getMessage());
                if ($linkId) {
                    return $resultRedirect->setPath('*/*/edit', ['id' => $linkId]);
                } else {
                    return $resultRedirect->setPath('*/*/newAction');
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(
                    __('Something went wrong while saving the link data. Please review the error log.')
                );
                $this->logger->critical($e);
                $this->dataPersistor->set(Link::PERSIST_NAME, $data);

                return $resultRedirect->setPath('*/*/edit', ['id' => $linkId]);
            }
        }

        return $resultRedirect->setPath('*/*/');
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function prepareData($data)
    {
        if (isset($data[LinkInterface::ENTITY_ID])) {
            $data[LinkInterface::ENTITY_ID] = (int)$data[LinkInterface::ENTITY_ID] ?: null;
        }

        // cms page id or landing page id saved into one column named page_id in db
        if (!$data[LinkInterface::PAGE_ID] && $data[LinkInterface::TYPE] == UrlKey::LANDING_PAGE) {
            $data[LinkInterface::PAGE_ID] = $data['landing_page'];
        }

        // unselect link type if link value not choosen
        if ($this->isLinkValueNotSelect($data)) {
            $data[LinkInterface::TYPE] = UrlKey::NO;
        }

        if (!$this->isLinkSelected((int) $data[LinkInterface::TYPE])) {
            $data[LinkInterface::LINK] = '';
        }

        return $data;
    }

    /**
     * @param array $data
     * @return bool
     */
    public function isLinkValueNotSelect($data)
    {
        $linkNotSelected = $this->isLinkSelected((int) $data[LinkInterface::TYPE]) && !$data[LinkInterface::LINK];
        $entityIdNotSelected = in_array($data[LinkInterface::TYPE], [UrlKey::CMS_PAGE, UrlKey::LANDING_PAGE])
            && !$data[LinkInterface::PAGE_ID];

        return $linkNotSelected || $entityIdNotSelected;
    }

    private function isLinkSelected(int $type): bool
    {
        return in_array($type, [UrlKey::LINK, UrlKey::EXTERNAL_URL]);
    }

    /**
     * @param $data
     *
     * @return \Amasty\MegaMenu\Model\Menu\Item
     */
    private function retrieveItemContent(&$data)
    {
        /** @var array $useDefaults */
        $useDefaults = $this->_request->getParam('use_default', []);
        $storeId = (int)$this->_request->getParam('store_id', 0);
        /** @var \Amasty\MegaMenu\Model\Menu\Item $itemContent */
        $itemContent = $this->itemFactory->create();
        if (isset($data['entity_id']) && $data['entity_id']) {
            $itemContentDefault = $this->itemRepository->getByEntityId(
                $data['entity_id'],
                0,
                'custom'
            );
            if ($storeId) {
                $itemContent->setStoreId($storeId);
                $itemContentTemp = $this->itemRepository->getByEntityId(
                    $data['entity_id'],
                    $storeId,
                    'custom'
                );
                if ($itemContentTemp) {
                    $itemContent = $itemContentTemp;
                }
            } else {
                $itemContent = $itemContentDefault;
            }
        }
        $itemContent->setType('custom');

        foreach (ItemInterface::FIELDS_BY_STORE_CUSTOM as $fieldSet) {
            foreach ($fieldSet as $field) {
                if (isset($data[$field])) {
                    if (isset($useDefaults[$field]) && $useDefaults[$field]) {
                        $itemContent->setData($field, null);
                    } else {
                        $itemContent->setData($field, $data[$field]);
                    }
                    unset($data[$field]);
                } else {
                    $this->triggerNotValid($field);
                }
            }
        }

        return $itemContent;
    }

    /**
     * @param $field
     *
     * @throws LocalizedException
     */
    private function triggerNotValid($field)
    {
        throw new LocalizedException(__('Please enter valid %1', $field));
    }
}
