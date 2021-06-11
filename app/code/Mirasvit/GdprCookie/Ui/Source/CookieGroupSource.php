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



namespace Mirasvit\GdprCookie\Ui\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\GdprCookie\Repository\CookieGroupRepository;

class CookieGroupSource implements OptionSourceInterface
{
    private $cookieGroupRepository;

    public function __construct(CookieGroupRepository $cookieGroupRepository)
    {
        $this->cookieGroupRepository = $cookieGroupRepository;
    }

    public function toOptionArray()
    {
        $options = [];

        $collection = $this->cookieGroupRepository->getCollection();

        foreach ($collection as $group) {
            $options[] = [
                'value' => $group->getId(),
                'label' => $group->getName(),
            ];
        }

        return $options;
    }
}
