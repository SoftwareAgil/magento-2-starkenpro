<?php
/**
 * Cart Layout Processor
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Cart;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{
    /**
     * @var \Magento\Checkout\Block\Checkout\AttributeMerger
     */
    protected $_merger;

    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    protected $_communeCollection;

    /**
     * @param \Magento\Checkout\Block\Checkout\AttributeMerger $merger
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection $communeCollection
     * @codeCoverageIgnore
     */
    public function __construct(
        \Magento\Checkout\Block\Checkout\AttributeMerger $merger,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection $communeCollection
    ) {
        $this->_merger = $merger;
        $this->_communeCollection = $communeCollection;
    }

    /**
     * Process js Layout of block
     *
     * @param array $jsLayout
     * @return array
     * @SuppressWarnings(PHPMD.NPathComplexity)
     */
    public function process($jsLayout)
    {
        $elements = [
            'commune_id' => [
                'component' => 'SoftwareAgil_StarkenPro/js/form/element/commune_id',
                'visible' => true,
                'formElement' => 'select',
                'label' => __('Commune'),
                'options' => [],
                'value' => null,
                'sortOrder' => 904
            ],
            'commune' => [
                'component' => 'SoftwareAgil_StarkenPro/js/form/element/commune',
                'visible' => false,
                'formElement' => 'text',
                'label' => __('Commune'),
                'value' => '',
                'sortOrder' => 906
            ],
            'postcode' => [
                'visible' => false,
                'formElement' => 'input',
                'label' => __('Zip/Postal Code'),
                'value' => '.',
                'sortOrder' => 908
            ]
        ];

        if (isset($jsLayout['components']['checkoutProvider']['dictionaries'])) {
            $jsLayout['components']['checkoutProvider']['dictionaries'] = [
                'country_id' => $jsLayout['components']['checkoutProvider']['dictionaries']['country_id'],
                'region_id' => $jsLayout['components']['checkoutProvider']['dictionaries']['region_id'],
                'commune_id' => $this->_communeCollection->toOptionArray()
            ];
        }
        if (isset($jsLayout['components']['block-summary']['children']['block-shipping']['children']
            ['address-fieldsets']['children'])
        ) {
            $fieldSetPointer = &$jsLayout['components']['block-summary']['children']['block-shipping']
            ['children']['address-fieldsets']['children'];
            $fieldSetPointer = $this->_merger->merge($elements, 'checkoutProvider', 'shippingAddress', $fieldSetPointer);
            $fieldSetPointer['region_id']['config']['skipValidation'] = true;
            $fieldSetPointer['commune_id']['component'] = 'SoftwareAgil_StarkenPro/js/form/element/commune_id';
            $fieldSetPointer['commune_id']['options'] = $jsLayout['components']['checkoutProvider']['dictionaries']['commune_id'];
        }
        return $jsLayout;
    }
}
