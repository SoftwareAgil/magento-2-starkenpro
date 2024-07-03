<?php
/**
 * Upgrade Data upgrade model
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    /**
     * {@inheritdoc}
     */
    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        /**
         * Create table 'sa_sp_city'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('sa_sp_city'))
            ->addColumn(
                'city_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'City ID'
            )
            ->addColumn(
                'sp_city_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'StarkenPro City ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Name'
            )
            ->addColumn(
                'region_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'Region ID'
            );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'sa_sp_commune'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('sa_sp_commune'))
            ->addColumn(
                'commune_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Commune ID'
            )
            ->addColumn(
                'sp_commune_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'StarkenPro Commune ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Name'
            )
            ->addColumn(
                'city_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'City ID'
            );
        $setup->getConnection()->createTable($table);

        /**
         * Create table 'sa_sp_agency'
         */
        $table = $setup->getConnection()
            ->newTable($setup->getTable('sa_sp_agency'))
            ->addColumn(
                'agency_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                'Agency ID'
            )
            ->addColumn(
                'sp_agency_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => true, 'nullable' => false],
                'StarkenPro Agency ID'
            )
            ->addColumn(
                'name',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Name'
            )
            ->addColumn(
                'address',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'default' => ''],
                'Address'
            )
            ->addColumn(
                'latitude',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'unsigned' => false, 'default' => '0'],
                'Latitude'
            )
            ->addColumn(
                'longitude',
                \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                255,
                ['nullable' => false, 'unsigned' => false, 'default' => '0'],
                'Longitude'
            )
            ->addColumn(
                'commune_id',
                \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                null,
                ['unsigned' => false, 'nullable' => false],
                'Commune ID'
            );
        $setup->getConnection()->createTable($table);

        $setup->endSetup();
    }
}
