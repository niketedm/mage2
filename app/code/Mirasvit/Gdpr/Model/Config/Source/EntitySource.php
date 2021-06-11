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



namespace Mirasvit\Gdpr\Model\Config\Source;

use Mirasvit\Gdpr\DataManagement\EntityRepository;

class EntitySource implements \Magento\Framework\Data\OptionSourceInterface
{
    private $entityRepository;

    public function __construct(EntityRepository $entityRepository)
    {
        $this->entityRepository = $entityRepository;
    }

    public function toOptionArray()
    {
        return array_map(function ($entity) {
            return [
                'label' => __($entity->getLabel()),
                'value' => $entity->getCode(),
            ];
        }, $this->entityRepository->getList());
    }
}
