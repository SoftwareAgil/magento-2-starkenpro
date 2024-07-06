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

use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\InstallDataInterface;
use Magento\Quote\Setup\QuoteSetupFactory;
use Magento\Customer\Model\Customer;

class InstallData implements InstallDataInterface
{

    private $quoteSetupFactory;
    private $customerSetupFactory;
    private $salesSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     */
    public function __construct(
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory,
	    CustomerSetupFactory $customerSetupFactory
    ) {
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
	    $this->customerSetupFactory = $customerSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function install(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'city_id', [
            'label' => 'City ID',
            'input' => 'select',
            'type' => 'int',
            'source' => 'SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source\City',
            'required' => true,
            'position' => 102,
            'visible' => true,
            'system' => false,
            'user_defined' => true,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);
        
        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'city_id')
        ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'checkout_onepage_billing_address',
                'checkout_onepage_shipping_address',
                'customer_account_edit'
            ]
        ]);
        $attribute->save();

        $quoteSetup->addAttribute('quote_address', 'city_id',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => false,
                'required' => false,
                'grid' => false
            ]
        );

        $salesSetup->addAttribute('order_address', 'city_id',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => false,
                'required' => false,
                'grid' => false
            ]
        );

        $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'commune_id', [
            'label' => 'Commune',
            'input' => 'select',
            'type' => 'int',
            'source' => 'SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source\Commune',
            'required' => true,
            'position' => 104,
            'visible' => true,
            'system' => false,
            'user_defined' => true,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'commune_id')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'checkout_onepage_billing_address',
                'checkout_onepage_shipping_address',
                'customer_account_edit'
            ]
            ]);
        $attribute->save();

        $quoteSetup->addAttribute('quote_address', 'commune_id',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => true,
                'required' => false,
                'grid' => false
            ]
        );

        $salesSetup->addAttribute('order_address', 'commune_id',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => true,
                'required' => false,
                'grid' => false
            ]
        );

        $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'commune', [
            'label' => 'Commune',
            'input' => 'text',
            'type' => 'varchar',
            'required' => true,
            'position' => 106,
            'visible' => true,
            'system' => false,
            'user_defined' => true,
            'is_used_in_grid' => false,
            'is_visible_in_grid' => false,
            'is_filterable_in_grid' => false,
            'is_searchable_in_grid' => false,
            'backend' => ''
        ]);

        $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'commune')
            ->addData(['used_in_forms' => [
                'adminhtml_customer_address',
                'customer_address_edit',
                'customer_register_address',
                'checkout_onepage_billing_address',
                'checkout_onepage_shipping_address',
                'customer_account_edit'
            ]
            ]);
        $attribute->save();

        $quoteSetup->addAttribute('quote_address', 'commune',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => true,
                'required' => false,
                'grid' => false
            ]
        );

        $salesSetup->addAttribute('order_address', 'commune',
            [
                'type' => 'varchar',
                'length' => 255,
                'visible' => true,
                'required' => false,
                'grid' => false
            ]
        );
    }
}
