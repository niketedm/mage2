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



namespace Mirasvit\Gdpr\DataManagement\Anonymizer;

class Anonymizer
{
    const ANONYMIZE_VALUE = 'Anonymized';

    /**
     * @param string $attributeCode
     *
     * @return string
     */
    public function getAnonymizeValue($attributeCode)
    {
        $salt = substr(hash('sha256', (string)microtime(true)), 0, 5);

        switch ($attributeCode) {
            case 'email':
            case 'customer_email':
                return strtolower(self::ANONYMIZE_VALUE . $salt . '@' . self::ANONYMIZE_VALUE . '.com');
            case 'dob':
            case 'customer_dob':
                return '1970-01-01';
            case 'gender':
                return 3;
            case 'telephone':
            case 'fax':
                return '0000000000';
            case 'country':
                return 'US';
        }

        return self::ANONYMIZE_VALUE;
    }
}
