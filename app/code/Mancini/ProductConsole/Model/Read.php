<?php
namespace Mancini\ProductConsole\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Magento\ImportExport\Helper\Report;
// use Magento\Tests\NamingConvention\true\string;
use Mancini\ProductConsole\Model\Source\Csv;
use Mancini\ProductConsole\Model\Source\CsvFactory;

class Read
{
    /** @var CsvFactory */
    protected $sourceCsvFactory;

    /** @var Filesystem */
    protected $filesystem;

    /**
     * @param CsvFactory $sourceCsvFactory
     * @param Filesystem $filesystem
     */
    public function __construct(
        CsvFactory $sourceCsvFactory,
        Filesystem $filesystem
    ) {
        $this->sourceCsvFactory = $sourceCsvFactory;
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $sourceFile
     * @return Csv
     */
    public function getCsv($sourceFile)
    {
        return $this->createSourceCsvModel($sourceFile);
    }

    /**
     * @param string $sourceFile
     * @return Csv
     */
    protected function createSourceCsvModel($sourceFile)
    {
        return $this->sourceCsvFactory->create(
            [
                'file' => $sourceFile,
                'directory' => $this->filesystem->getDirectoryRead(DirectoryList::VAR_DIR)
            ]
        );
    }
}
