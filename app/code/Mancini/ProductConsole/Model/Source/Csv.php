<?php
/**
 * Copyright © 2016 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Mancini\ProductConsole\Model\Source;

use LogicException;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Filesystem\Directory\Read;
use Magento\Framework\Filesystem\File\Write;
use Mancini\ProductConsole\Model\AbstractSource;

/**
 * CSV import adapter
 */
class Csv extends AbstractSource
{
    /**
     * @var Write
     */
    protected $_file;

    /**
     * Delimiter.
     *
     * @var string
     */
    protected $_delimiter = ',';

    /**
     * @var string
     */
    protected $_enclosure = '';

    /**
     * Open file and detect column names
     *
     * There must be column names in the first line
     *
     * @param string $file
     * @param Read $directory
     * @param string $delimiter
     * @param string $enclosure
     * @throws LogicException
     */
    public function __construct(
        $file,
        Read $directory,
        $delimiter = ',',
        $enclosure = '"'
    )
    {
        register_shutdown_function([$this, 'destruct']);
        try {
            $this->_file = $directory->openFile($directory->getRelativePath($file), 'r');
        } catch (FileSystemException $e) {
            throw new LogicException("Unable to open file: '{$file}'");
        }
        if ($delimiter) {
            $this->_delimiter = $delimiter;
        }
        $this->_enclosure = $enclosure;
        parent::__construct($this->_getNextRow());
    }

    /**
     * Read next line from CSV-file
     *
     * @return array|bool
     */
    protected function _getNextRow()
    {
        $parsed = $this->_file->readCsv(0, $this->_delimiter, $this->_enclosure);
        if (is_array($parsed) && count($parsed) != $this->_colQty) {
            foreach ($parsed as $element) {
                if (strpos($element, "'") !== false) {
                    $this->_foundWrongQuoteFlag = true;
                    break;
                }
            }
        } else {
            $this->_foundWrongQuoteFlag = false;
        }
        return is_array($parsed) ? $parsed : [];
    }

    /**
     * Close file handle
     *
     * @return void
     */
    public function destruct()
    {
        if (is_object($this->_file)) {
            $this->_file->close();
        }
    }

    /**
     * Rewind the \Iterator to the first element (\Iterator interface)
     *
     * @return void
     */
    public function rewind()
    {
        $this->_file->seek(0);
        $this->_getNextRow();
        // skip first line with the header
        parent::rewind();
    }
}
