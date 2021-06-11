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
 * @package   mirasvit/module-navigation
 * @version   1.1.0
 * @copyright Copyright (C) 2020 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\LayeredNavigation\Service;

class JsonService
{
    /**
     * @param array $data
     * @return false|string
     */
    public function serialize($data)
    {
        $result = json_encode($data);
        if (false === $result) {
            throw new \InvalidArgumentException('Unable to serialize value.');
        }

        return $result;
    }

    /**
     * @param string $string
     * @return array
     */
    public function unserialize($string)
    {
        $result = json_decode($string, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Unable to unserialize value.');
        }

        return $result;
    }
}
