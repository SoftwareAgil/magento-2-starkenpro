<?php

namespace SoftwareAgil\StarkenPro\Setup;

use Magento\Framework\Setup\UninstallInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class Uninstall implements UninstallInterface
{
    public function uninstall(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        //--Remove Tables
        if ($setup->tableExists('sa_sp_city')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_city'));
        }
        if ($setup->tableExists('sa_sp_commune')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_commune'));
        }
        if ($setup->tableExists('sa_sp_agency')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_agency'));
        }
        if ($setup->tableExists('sa_sp_merchant_account')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_merchant_account'));
        }
        if ($setup->tableExists('sa_sp_api_calls_log')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_api_calls_log'));
        }
        if ($setup->tableExists('sa_sp_merchant_account')) {
            $setup->getConnection()->dropTable($setup->getTable('sa_sp_merchant_account'));
        }
        //--Remove Tables
        //--Remove columns - sales_shipment
        if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_shipment'), 'sa_sp_shipping_label_id')) {
            $setup->getConnection()->dropColumn($setup->getTable('sales_shipment'), 'sa_sp_shipping_label_id');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_shipment'), 'sa_sp_freight_order')) {
            $setup->getConnection()->dropColumn($setup->getTable('sales_shipment'), 'sa_sp_freight_order');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_shipment'), 'sa_sp_freight_order_content')) {
            $setup->getConnection()->dropColumn($setup->getTable('sales_shipment'), 'sa_sp_freight_order_content');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('sales_shipment'), 'sa_sp_tracking_content')) {
            $setup->getConnection()->dropColumn($setup->getTable('sales_shipment'), 'sa_sp_tracking_content');
        }
        //--Remove columns - customer_address_entity
        //--Remove columns - customer_address_entity
        if ($setup->getConnection()->tableColumnExists($setup->getTable('customer_address_entity'), 'commune_id')) {
            $setup->getConnection()->dropColumn($setup->getTable('customer_address_entity'), 'commune_id');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('customer_address_entity'), 'commune')) {
            $setup->getConnection()->dropColumn($setup->getTable('customer_address_entity'), 'commune');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('customer_address_entity'), 'rut')) {
            $setup->getConnection()->dropColumn($setup->getTable('customer_address_entity'), 'rut');
        }
        if ($setup->getConnection()->tableColumnExists($setup->getTable('customer_address_entity'), 'agency_id')) {
            $setup->getConnection()->dropColumn($setup->getTable('customer_address_entity'), 'agency_id');
        }
        //--Remove columns - customer_address_entity
        //--Remove Index - directory_country_region
        $indexdcr = $setup->getIdxName(
            $setup->getTable('directory_country_region'),
            ['code', 'default_name'],
            \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
        );
        if ($setup->getConnection()->isIndexExists('directory_country_region', $indexdcr)) {
            $setup->getConnection()->dropIndex('directory_country_region', $indexdcr);
        }
        //--Remove Index - directory_country_region
        
        $setup->endSetup();
    }
}