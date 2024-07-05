<?php

namespace SoftwareAgil\StarkenPro\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UninstallDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Customer\Model\Customer;
use Magento\Eav\Setup\EavSetupFactory;

class UninstallData implements UninstallDataInterface
{
    public function __construct(
        private QuoteSetupFactory $quoteSetupFactory,
        private SalesSetupFactory $salesSetupFactory,
	    private CustomerSetupFactory $customerSetupFactory,
        private EavSetupFactory $eavSetupFactory,
    ) {}

    public function uninstall(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $setup->startSetup();

        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        //--city id
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'city_id'
        );
        if ($attribute && $attribute->getId()) {
            $attribute->setUsedInForms([]);
            $attribute->save();
        }

        $customerSetup->removeAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'city_id');

        $quoteSetup->removeAttribute('quote_address', 'city_id');
        $salesSetup->removeAttribute('order_address', 'city_id');
        //--city id
        //--commune id
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'commune_id'
        );
        if ($attribute && $attribute->getId()) {
            $attribute->setUsedInForms([]);
            $attribute->save();
        }

        $customerSetup->removeAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'commune_id');

        $quoteSetup->removeAttribute('quote_address', 'commune_id');
        $salesSetup->removeAttribute('order_address', 'commune_id');
        //--commune id
        //--commune
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'commune'
        );
        if ($attribute && $attribute->getId()) {
            $attribute->setUsedInForms([]);
            $attribute->save();
        }

        $customerSetup->removeAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'commune');

        $quoteSetup->removeAttribute('quote_address', 'commune');
        $salesSetup->removeAttribute('order_address', 'commune');
        //--commune
        //--rut
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'rut'
        );
        if ($attribute && $attribute->getId()) {
            $attribute->setUsedInForms([]);
            $attribute->save();
        }

        $customerSetup->removeAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'rut');

        $quoteSetup->removeAttribute('quote_address', 'rut');
        $salesSetup->removeAttribute('order_address', 'rut');
        //--rut
        //--agency_id
        $attribute = $customerSetup->getEavConfig()->getAttribute(
            \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
            'agency_id'
        );
        if ($attribute && $attribute->getId()) {
            $attribute->setUsedInForms([]);
            $attribute->save();
        }

        $customerSetup->removeAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'agency_id');

        $quoteSetup->removeAttribute('quote_address', 'agency_id');
        $salesSetup->removeAttribute('order_address', 'agency_id');
        //--agency_id


        $setup->endSetup();
    }
}