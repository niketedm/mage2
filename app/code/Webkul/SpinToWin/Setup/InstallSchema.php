<?php
/**
 * Webkul Software.
 *
 * @category  Webkul
 * @package   Webkul_SpinToWin
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\SpinToWin\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

/**
 * @codeCoverageIgnore
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * {@inheritdoc}
     *
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->moveDirToMediaDir();

        $installer = $setup;
        $installer->startSetup();

        /*
         * Create table 'spintowin_info'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_info'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'name'
            )
            // ->addColumn(
            //     'terms',
            //     \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            //     null,
            //     [],
            //     'Terms & Conditions'
            // )
            ->addColumn(
                'website_ids',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'websites where available'
            )
            ->addColumn(
                'start_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'Start Date'
            )
            ->addColumn(
                'end_date',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                [],
                'End Date'
            )
            ->addColumn(
                'scheduled',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => '0'],
                'Scheduled'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default' => '0'],
                'Status'
            )
            // ->addColumn(
            //     'budget',
            //     \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            //     null,
            //     ['default' => '0'],
            //     'Budget'
            // )
            ->setComment('Spin to win information Table');
        $installer->getConnection()->createTable($table);
        
        /*
         * Create table 'spintowin_editform'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_editform'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'background color'
            )
            ->addColumn(
                'text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#333333'],
                'text color'
            )
            ->addColumn(
                'logo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'brand logo'
            )
            ->addColumn(
                'heading',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'heading'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'description'
            )
            ->addColumn(
                'cname_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'customer name enable?'
            )
            ->addColumn(
                'cname_required',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'customer name required?'
            )
            ->addColumn(
                'cname_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Name'],
                'customer name label'
            )
            ->addColumn(
                'cemail_status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'customer email enable?'
            )
            ->addColumn(
                'cemail_required',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'customer email required?'
            )
            ->addColumn(
                'cemail_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Email Address'],
                'customer email label'
            )
            ->addColumn(
                'button_background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FC3367'],
                'button background color'
            )
            ->addColumn(
                'button_text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'button text color'
            )
            ->addColumn(
                'button_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Spin Wheel'],
                'button label'
            )
            ->addColumn(
                'show_progress',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'Show progress meter'
            )
            ->addColumn(
                'progress_percent',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default'=>35],
                'progress meter percentage'
            )
            ->addColumn(
                'progress_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'35% Offers Claimed, Hurry Up!'],
                'progress label'
            )
            ->setComment('Spin to win edit form Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_resultform'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_resultform'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'background color'
            )
            ->addColumn(
                'text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#333333'],
                'text color'
            )
            ->addColumn(
                'logo',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'brand logo'
            )
            ->addColumn(
                'coupon_background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#E3FEDF'],
                'coupon background color'
            )
            ->addColumn(
                'coupon_text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#31582D'],
                'coupon text color'
            )
            ->addColumn(
                'coupon_button_background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#23A900'],
                'coupon button background color'
            )
            ->addColumn(
                'coupon_button_text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'coupon button text color'
            )
            ->addColumn(
                'button_background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FF3131'],
                'button background color'
            )
            ->addColumn(
                'button_text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'button text color'
            )
            ->addColumn(
                'button_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Shop Now'],
                'button label'
            )
            ->setComment('Spin to win edit form Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_wheel'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_wheel'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#5355FB'],
                'background color'
            )
            ->addColumn(
                'background_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'background image'
            )
            ->addColumn(
                'background_image_repeat',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'repeat'],
                'background image repeat property'
            )
            ->addColumn(
                'inner_wheel',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'Show Inner Wheel'
            )
            ->addColumn(
                'inner_radius',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default'=>30],
                'Inner Wheel Radius'
            )
            ->addColumn(
                'center_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'center logo'
            )
            ->addColumn(
                'center_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#00FFDE'],
                'center board color'
            )
            ->addColumn(
                'result_background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#fbc602'],
                'result segment background color'
            )
            ->addColumn(
                'result_text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#9e5201'],
                'result segment text color'
            )
            ->addColumn(
                'pin_image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'spintowin/image/red_pin.png'],
                'Pin Image'
            )
            ->addColumn(
                'stroke_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'strokeStyle'
            )
            ->addColumn(
                'stroke_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'strokeStyle'
            )
            ->addColumn(
                'text_direction',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'normal'],
                'Text Direction'
            )
            ->addColumn(
                'font_size',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                4,
                ['default'=>14],
                'segment font size'
            )
            ->addColumn(
                'segments',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'segments ["background-color,text-color"]'
            )
            ->setComment('Spin to win wheel Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_layout'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_layout'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'view',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'popup'],
                'View type'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                [],
                'Position'
            )
            ->addColumn(
                'wheel_view',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'full'],
                'Spin wheel view'
            )
            ->addColumn(
                'trigger_button_position',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'bottom-right'],
                'brand logo'
            )
            ->setComment('Spin to win layout Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_visibility'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_visibility'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'wheel',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Spin wheel visibility'
            )
            ->addColumn(
                'button',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Spin to win button visibility'
            )
            ->addColumn(
                'events',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                50,
                ['default'=>'immediate'],
                'Spin to win visibility events'
            )
            ->setComment('Spin to win visibility Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_button'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_button'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'show',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'Show spin to win button'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Spin to Win'],
                'Spin to win button label'
            )
            ->addColumn(
                'background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#000000'],
                'Spin to win button background color'
            )
            ->addColumn(
                'text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'Spin to win button text color'
            )
            ->addColumn(
                'image',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'spintowin/image/red.png'],
                'Spin to win image'
            )
            ->setComment('Spin to win button Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_coupon'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_coupon'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'show',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['default'=>1],
                'Show spin to win coupon'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>'Won Coupon'],
                'Spin to win coupon label'
            )
            ->addColumn(
                'background_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#f94c4c'],
                'Spin to win coupon background color'
            )
            ->addColumn(
                'text_color',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                20,
                ['default'=>'#FFFFFF'],
                'Spin to win coupon text color'
            )
            ->setComment('Spin to win coupon Table');
        $installer->getConnection()->createTable($table);

        /**
         * Add Spin Campaign ID column to salesrule table
         */
        $connection = $installer->getConnection();
        $connection->addColumn(
            $installer->getTable('salesrule'),
            'segment_id',
            [
                'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                'nullable' => true,
                'default' => null,
                'comment' => 'Spin Campaign Segment Id '
            ]
        );

        /*
         * Create table 'spintowin_segments'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_segments'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'rule_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Rule ID'
            )
            ->addColumn(
                'type',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                [],
                'Segment Type'
            )
            ->addColumn(
                'label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Segment label'
            )
            ->addColumn(
                'heading',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Segment heading'
            )
            ->addColumn(
                'description',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Segment description'
            )
            ->addColumn(
                'limits',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Limit'
            )
            ->addColumn(
                'availed',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => 0],
                'Availed number of times'
            )
            ->addColumn(
                'gravity',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Gravity/Probability'
            )
            ->addColumn(
                'position',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false, 'default' => 0],
                'Position on the spin wheel'
            )
            // ->addColumn(
            //     'value',
            //     \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            //     null,
            //     ['nullable' => false],
            //     'Coupon Value for Budget'
            // )
            ->setComment('Spin to win segments Table');
        $installer->getConnection()->createTable($table);

        /*
         * Create table 'spintowin_reports'
         */
        $table = $installer->getConnection()
            ->newTable($installer->getTable('spintowin_reports'))
            ->addColumn(
                'entity_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Entity ID'
            )
            ->addColumn(
                'spin_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Spin Campaign ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>null],
                'Customer Name'
            )
            ->addColumn(
                'email',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                null,
                [],
                'Customer Email'
            )
            ->addColumn(
                'timestamp',
                \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP,
                null,
                ['nullable' => false, 'default' => \Magento\Framework\DB\Ddl\Table::TIMESTAMP_INIT],
                'Spined at'
            )
            ->addColumn(
                'result',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Result'
            )
            ->addColumn(
                'coupon',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['default'=>''],
                'Coupon Code'
            )
            ->addColumn(
                'status',
                \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
                null,
                ['nullable' => false],
                'Status'
            )
            ->addColumn(
                'order_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Order ID'
            )
            ->addColumn(
                'order_amount',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Order Amount'
            )
            ->addColumn(
                'order_discount',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['default' => null],
                'Order Discount'
            )
            ->addColumn(
                'segment_label',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                250,
                ['nullable' => false],
                'Segment Label'
            )
            ->addColumn(
                'segment_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['nullable' => false],
                'Segment Id'
            )
            ->setComment('Spin to win reports Table');
        $installer->getConnection()->createTable($table);
        
        $installer->endSetup();
    }

    private function moveDirToMediaDir()
    {
        try {
            $objManager = \Magento\Framework\App\ObjectManager::getInstance();
            $reader = $objManager->get('Magento\Framework\Module\Dir\Reader');
            $filesystem = $objManager->get('Magento\Framework\Filesystem');
            $fileDriver = $objManager->get('Magento\Framework\Filesystem\Driver\File');

            $type = \Magento\Framework\App\Filesystem\DirectoryList::MEDIA;
            $smpleFilePath = $filesystem->getDirectoryRead($type)
                                        ->getAbsolutePath().'spintowin/image/';
            $files = [
                'red.png',
                'green.png',
                'blue.png',
                'purple.png',
                'yellow.png',
                'wheel.png',
                'red_pin.png',
                'green_pin.png',
                'yellow_pin.png',
                'purple_pin.png',
                'coupon.png'
            ];
            if ($fileDriver->isExists($smpleFilePath)) {
                $fileDriver->deleteDirectory($smpleFilePath);
            }
            if (!$fileDriver->isExists($smpleFilePath)) {
                $fileDriver->createDirectory($smpleFilePath, 0777);
            }
            foreach ($files as $file) {
                $filePath = $smpleFilePath.$file;
                if (!$fileDriver->isExists($filePath)) {
                    $path = '/view/base/web/image/'.$file;
                    $mediaFile = $reader->getModuleDir('', 'Webkul_SpinToWin').$path;
                    if ($fileDriver->isExists($mediaFile)) {
                        $fileDriver->copy($mediaFile, $filePath);
                    }
                }
            }
        } catch (\Exception $e) {
        }
    }
}
