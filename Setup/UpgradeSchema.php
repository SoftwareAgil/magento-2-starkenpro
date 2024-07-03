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

use Magento\Framework\Setup\UpgradeSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class UpgradeSchema implements UpgradeSchemaInterface
{
    /**
     * {@inheritdoc}
     */
    public function upgrade(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $setup->startSetup();
        if (version_compare($context->getVersion(), "1.0.1", "<")) {
            /**
             * Create table 'sa_sp_merchant_account'
             */
            $table = $setup->getConnection()
                ->newTable($setup->getTable('sa_sp_merchant_account'))
                ->addColumn(
                    'id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['identity' => true, 'unsigned' => true, 'nullable' => false, 'primary' => true],
                    'ID'
                )
                ->addColumn(
                    'rut',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'RUT'
                )
                ->addColumn(
                    'checking_account',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false],
                    'Checking Account'
                )
                ->addColumn(
                    'checking_account_dv',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Checking Account DV'
                )
                ->addColumn(
                    'cost_center',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    ['nullable' => false],
                    'Cost Center'
                );
            $setup->getConnection()->createTable($table);

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment'),
                'sa_sp_shipping_label_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Shipping Label ID'
                ]
            );

            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment'),
                'sa_sp_freight_order',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Freight Order'
                ]
            );

        }

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sa_sp_merchant_account'),
                'cost_center_name',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Cost Center Name'
                ]
            );
        }

        if (version_compare($context->getVersion(), "1.0.3", "<")) {
            $setup->getConnection()->changeColumn(
                $setup->getTable('sa_sp_merchant_account'),
                'checking_account_dv',
                'checking_account_dv',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => false],
                false
            );
        }

        if (version_compare($context->getVersion(), "1.0.4", "<")) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment'),
                'sa_sp_freight_order_content',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Freight Order Content'
                ]
            );
            $setup->getConnection()->addColumn(
                $setup->getTable('sales_shipment'),
                'sa_sp_tracking_content',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'Shipment Tracking Content'
                ]
            );
        }
        if (version_compare($context->getVersion(), "1.0.5", "<")) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sa_sp_commune'),
                'region_id',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    'nullable' => true,
                    'comment' => 'Region ID'
                ]
            );
            $setup->getConnection()->dropTable('sa_sp_city');
        }
        if (version_compare($context->getVersion(), "1.0.6", "<")) {
            //Simulating static custom attributes in Magento 2.2.
            $customStaticAttributes = [
                'commune_id' => [\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'Commune'],
                'commune' => [\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'Commune'],
                'rut' => [\Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'RUT'],
                'agency_id' => [\Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, 'Agency ID']
            ];
            foreach ($customStaticAttributes as $attributeCode => $attributeProperties) {
                $setup->getConnection()->addColumn(
                    $setup->getTable('customer_address_entity'),
                    $attributeCode,
                    [
                        'type' => $attributeProperties[0],
                        'nullable' => true,
                        'comment' => $attributeProperties[1]
                    ]
                );
            }
        }
        if (version_compare($context->getVersion(), "1.0.7", "<")) {
            $setup->getConnection()->addIndex(
                $setup->getTable('directory_country_region'),
                $setup->getIdxName(
                    $setup->getTable('directory_country_region'),
                    ['code', 'default_name'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['code', 'default_name'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('sa_sp_commune'),
                $setup->getIdxName(
                    $setup->getTable('sa_sp_commune'),
                    ['sp_commune_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['sp_commune_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
            $setup->getConnection()->addIndex(
                $setup->getTable('sa_sp_agency'),
                $setup->getIdxName(
                    $setup->getTable('sa_sp_agency'),
                    ['sp_agency_id'],
                    \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                ),
                ['sp_agency_id'],
                \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
            );
        }

        if (version_compare($context->getVersion(), "1.0.8", "<")) {
            if (!$setup->tableExists('sa_sp_api_calls_log')) {
                $table = $setup->getConnection()
                    ->newTable($setup->getTable('sa_sp_api_calls_log'))
                    ->addColumn('id', \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER, null, [
                        'identity' => true,
                        'nullable' => false,
                        'primary'  => true,
                        'unsigned' => true,
                    ], 'Log ID')
                    ->addColumn('endpoint', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 255, ['nullable => false'], 'Email Subject')
                    ->addColumn('http_code', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 10, ['nullable => false'], 'Response HTTP Code')
                    ->addColumn('request', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Request')
                    ->addColumn('response', \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, '64k', [], 'Response')
                    ->addColumn('created_at', \Magento\Framework\DB\Ddl\Table::TYPE_TIMESTAMP, null, [], 'Created At')
                    ->addIndex($setup->getIdxName('sa_sp_api_calls_log', ['created_at']), ['created_at']);

                $setup->getConnection()->createTable($table);
            }
        }

        if (version_compare($context->getVersion(), "1.0.11", "<")) {
            $setup->getConnection()->addColumn(
                $setup->getTable('sa_sp_merchant_account'),
                'user_token',
                [
                    'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    'nullable' => true,
                    'comment' => 'User Token'
                ]
            );
        }

        if (version_compare($context->getVersion(), "1.0.12", "<")) {
            $setup->run("ALTER TABLE ".$setup->getTable('sa_sp_api_calls_log')." MODIFY COLUMN response MEDIUMTEXT COMMENT 'Response'");
            $setup->run("ALTER TABLE ".$setup->getTable('sales_shipment')." MODIFY COLUMN sa_sp_freight_order_content MEDIUMTEXT COMMENT 'Freight Order Content'");
            $setup->run("ALTER TABLE ".$setup->getTable('sales_shipment')." MODIFY COLUMN sa_sp_tracking_content MEDIUMTEXT COMMENT 'Shipment Tracking Content'");
            $setup->getConnection()->changeColumn(
                $setup->getTable('sa_sp_merchant_account'),
                'checking_account',
                'checking_account',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => true],
                false
            );
            $setup->getConnection()->changeColumn(
                $setup->getTable('sa_sp_merchant_account'),
                'checking_account_dv',
                'checking_account_dv',
                ['type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT, 'nullable' => true],
                false
            );
        }

        $setup->endSetup();
    }
}
