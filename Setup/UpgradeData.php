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

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Customer\Setup\CustomerSetupFactory;
use Magento\Eav\Setup\EavSetupFactory;
use Magento\Sales\Setup\SalesSetupFactory;
use Magento\Quote\Setup\QuoteSetupFactory;

class UpgradeData implements UpgradeDataInterface
{
    private $customerSetupFactory;
    private $quoteSetupFactory;
    private $salesSetupFactory;
    protected $eavSetupFactory;

    /**
     * Constructor
     *
     * @param \Magento\Customer\Setup\CustomerSetupFactory $customerSetupFactory
     * @param \Magento\Eav\Setup\EavSetupFactory $eavSetupFactory
     * @param \Magento\Quote\Setup\QuoteSetupFactory $quoteSetupFactory
     * @param \Magento\Sales\Setup\SalesSetupFactory $salesSetupFactory
     */
    public function __construct(
        CustomerSetupFactory $customerSetupFactory,
        EavSetupFactory $eavSetupFactory,
        QuoteSetupFactory $quoteSetupFactory,
        SalesSetupFactory $salesSetupFactory
    ) {
        $this->customerSetupFactory = $customerSetupFactory;
        $this->eavSetupFactory = $eavSetupFactory;
        $this->quoteSetupFactory = $quoteSetupFactory;
        $this->salesSetupFactory = $salesSetupFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function upgrade(
        ModuleDataSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $customerSetup = $this->customerSetupFactory->create(['setup' => $setup]);
        $quoteSetup = $this->quoteSetupFactory->create(['setup' => $setup]);
        $salesSetup = $this->salesSetupFactory->create(['setup' => $setup]);
        $eavSetup = $this->eavSetupFactory->create(['setup' => $setup]);

        if (version_compare($context->getVersion(), "1.0.2", "<")) {
            $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'rut', [
                'label' => 'RUT',
                'input' => 'text',
                'type' => 'varchar',
                'required' => true,
                'position' => 108,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'rut')
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
            $quoteSetup->addAttribute('quote_address', 'rut',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'visible' => true,
                    'required' => false,
                    'grid' => false
                ]
            );
            $salesSetup->addAttribute('order_address', 'rut',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'visible' => true,
                    'required' => false,
                    'grid' => false
                ]
            );
            $customerSetup->addAttribute(\Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY, 'agency_id', [
                'label' => 'Agency ID',
                'input' => 'select',
                'type' => 'int',
                'source' => 'SoftwareAgil\StarkenPro\Model\ResourceModel\Address\Attribute\Source\Agency',
                'required' => false,
                'position' => 107,
                'visible' => true,
                'system' => false,
                'user_defined' => true,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'is_searchable_in_grid' => false,
                'backend' => ''
            ]);
            $attribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'agency_id')
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
            $quoteSetup->addAttribute('quote_address', 'agency_id',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'visible' => true,
                    'required' => false,
                    'grid' => false
                ]
            );
            $salesSetup->addAttribute('order_address', 'agency_id',
                [
                    'type' => 'varchar',
                    'length' => 255,
                    'visible' => true,
                    'required' => false,
                    'grid' => false
                ]
            );
            //Assigning Attribute Set for customer address attributes
            $attributes = ['city_id', 'commune_id', 'commune', 'agency_id', 'rut'];
            foreach($attributes as $attributeCode) {
                $eavSetup->addAttributeToSet(
                    \Magento\Customer\Api\AddressMetadataInterface::ENTITY_TYPE_ADDRESS,
                    \Magento\Customer\Api\AddressMetadataInterface::ATTRIBUTE_SET_ID_ADDRESS,
                    null, $attributeCode);
            }
        }

        if (version_compare($context->getVersion(), "1.0.5", "<")) {
            $cityAttribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'city');
            $cityAttribute->setIsRequired(0);
            $cityAttribute->save();
            $eavSetup->removeAttribute(
                \Magento\Customer\Model\Indexer\Address\AttributeProvider::ENTITY,
                'city_id'
            );
        }

        if (version_compare($context->getVersion(), "1.0.9", "<")) {
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sp_dimensions_length', [
                'type' => 'decimal',
                'label' => 'Starken - Length',
                'input' => 'text',
                'required' => false,
                'class' => 'not-negative-amount',
                'sort_order' => 65,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'user_defined' => false,
                'apply_to' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ]);
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sp_dimensions_width', [
                'type' => 'decimal',
                'label' => 'Starken - Width',
                'input' => 'text',
                'required' => false,
                'class' => 'not-negative-amount',
                'sort_order' => 66,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'user_defined' => false,
                'apply_to' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ]);
            $eavSetup->addAttribute(\Magento\Catalog\Model\Product::ENTITY, 'sp_dimensions_height', [
                'type' => 'decimal',
                'label' => 'Starken - Height',
                'input' => 'text',
                'required' => false,
                'class' => 'not-negative-amount',
                'sort_order' => 67,
                'global' => \Magento\Eav\Model\Entity\Attribute\ScopedAttributeInterface::SCOPE_STORE,
                'is_used_in_grid' => false,
                'is_visible_in_grid' => false,
                'is_filterable_in_grid' => false,
                'user_defined' => false,
                'apply_to' => \Magento\Catalog\Model\Product\Type::TYPE_SIMPLE
            ]);
        }

        if (version_compare($context->getVersion(), "1.0.10", "<")) {
            $communeIdAttribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'commune_id');
            $communeIdAttribute->setIsRequired(0);
            $communeIdAttribute->save();
            $communeAttribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'commune');
            $communeAttribute->setIsRequired(0);
            $communeAttribute->save();
            $rutAttribute = $customerSetup->getEavConfig()->getAttribute('customer_address', 'rut');
            $rutAttribute->setIsRequired(0);
            $rutAttribute->save();
        }
    }
}
