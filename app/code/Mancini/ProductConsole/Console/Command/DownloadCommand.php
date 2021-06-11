<?php

namespace Mancini\ProductConsole\Console\Command;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Command for displaying information related to indexers.
 */
class DownloadCommand extends AbstractCommand
{
    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this->setName('product_console:download')
            ->setDescription('Auto Update Product Data')
            ->setDefinition([
                new InputArgument(
                    'file',
                    InputArgument::OPTIONAL,
                    'File Name'
                ),
                new InputArgument(
                    'path',
                    InputArgument::OPTIONAL,
                    'File Path'
                )
            ]);
    }

    /**
     * {@inheritdoc}
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        //$filename = $this->getImportFileName($input);
        //$file = $this->getFile($input);
        $helper = $this->getObjectManager()->create('Mancini\ProductConsole\Helper\Data');

        $ftp_host = $helper->getStoreConfigValue('product_console/ftp_settings/ftp_host');//'64.79.127.182';
        $ftp_user_name = $helper->getStoreConfigValue('product_console/ftp_settings/ftp_user_name');//'Export-User';
        $ftp_user_pass = $helper->getStoreConfigValue('product_console/ftp_settings/ftp_user_pass');//'Export@User';
        $ssl = $helper->getStoreConfigValue('product_console/ftp_settings/use_ssl');
        $remote_file = $helper->getStoreConfigValue('product_console/ftp_settings/remote_file');
        $port = 21;
        if (strrpos($ftp_host, ':')) {
            $pa = explode(':', $ftp_host);
            $port = $pa[1];
            $ftp_host = $pa[0];
        }

        if ($ssl) {
            $conn_id = ftp_ssl_connect($ftp_host);
        } else {
            $conn_id = ftp_connect($ftp_host, $port);
        }

        $login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);
        if (!$login_result) {
            echo "Can not login to ftp:" . $ftp_host . "\n";
            ftp_close($conn_id);
            exit();
        }
        ftp_pasv($conn_id, true);
        /** @var Filesystem $fileSystem */
        $fileSystem = $this->getObjectManager()->create('Magento\Framework\Filesystem');
        $directoryWrite = $fileSystem->getDirectoryWrite(DirectoryList::VAR_DIR);
        $varPath = $directoryWrite->getAbsolutePath();
        $directoryWrite->create('exportDownload');

        $localFile = $varPath . '/exportDownload' . '/export-' . date('Y-m-d') . '.csv';
        $ftp = array(
            'local_file' => $localFile,
            'remote_file' => $remote_file
        );

        if (ftp_get($conn_id, $ftp['local_file'], $ftp['remote_file'], FTP_BINARY)) {
            echo "successfully downloaded " . $ftp['remote_file'] . "\n";
        } else {
            echo "There was a problem while downloading " . $ftp['remote_file'] . "\n";
            //errorLog("There was a problem while downloading ".$ftp['filename']);
        }

        ftp_close($conn_id);
    }

    /**
     * Return download file with path
     *
     * @return string $filepath
     */
    protected function getFile(InputInterface $input)
    {
        $file = '';
        if ($input->getArgument('file')) {
            $file = $input->getArgument('file');
            //$requestedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        } else {
            $file = 'EXPORT.CSV';
        }
        $path = '';
        if ($input->getArgument('path')) {
            $path = trim($input->getArgument('path'), '/');
            $path .= '/';
            //$requestedTypes = array_filter(array_map('trim', $requestedTypes), 'strlen');
        }

        return $path . $file;
    }
}
