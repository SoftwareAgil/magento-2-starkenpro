<?php
/**
 * Layout Processor
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
namespace SoftwareAgil\StarkenPro\Block\Checkout;

class LayoutProcessor implements \Magento\Checkout\Block\Checkout\LayoutProcessorInterface
{

    /**
     * @var \SoftwareAgil\StarkenPro\Block\Checkout\AttributeMerger
     */
    protected $_merger;

    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection
     */
    protected $_communeCollection;

    /**
     * @var \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection
     */
    protected $_agencyCollection;

    /**
     * @param \SoftwareAgil\StarkenPro\Block\Checkout\AttributeMerger $merger
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection $communeCollection
     * @param \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection $communeCollection
     * @codeCoverageIgnore
     */
    public function __construct(
        \SoftwareAgil\StarkenPro\Block\Checkout\AttributeMerger $merger,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Commune\Collection $communeCollection,
        \SoftwareAgil\StarkenPro\Model\ResourceModel\Agency\Collection $agencyCollection
    ) {
        $this->_merger = $merger;
        $this->_communeCollection = $communeCollection;
        $this->_agencyCollection = $agencyCollection;
    }

    public function getShippingFormFields($result)
    {
        $resultShippingAddress = $result;
        if(isset($result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'])
        ) {
            $resultShippingAddress = $result['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset'];
        } else {
            return $result;
        }

        $customShippingFields = $this->getFields('shippingAddress.custom_attributes','shipping');
        $shippingFields = $resultShippingAddress['children'];
        $shippingFields = array_replace_recursive($shippingFields,$customShippingFields);
        $resultShippingAddress['children'] = $shippingFields;

        if (isset($resultShippingAddress['children']['city'])) {
            $resultShippingAddress['children']['city']['visible'] = false;
        }

        if (isset($resultShippingAddress['children']['postcode'])) {
            $resultShippingAddress['children']['postcode']['visible'] = false;
            $resultShippingAddress['children']['postcode']['validation']['required-entry'] = false;
            $resultShippingAddress['children']['postcode']['value'] = '';
        }

        if (isset($resultShippingAddress['children']['country_id'])) {
            $resultShippingAddress['children']['country_id']['sortOrder'] = 90;
        }

        if (isset($resultShippingAddress['children']['street'])) {
            $resultShippingAddress['children']['street']['sortOrder'] = 106;
            $resultShippingAddress['children']['street']['children'][0]['placeholder'] = __('Street');
            $resultShippingAddress['children']['street']['children'][1]['placeholder'] = __('Number');
            $resultShippingAddress['children']['street']['children'][2]['placeholder'] = __('Complement');
            $resultShippingAddress['children']['street']['children'][1]['validation'] = ['required-entry' => true, 'validate-number' => true];
        }

        $result['components']['checkout']['children']['steps']['children']
        ['shipping-step']['children']['shippingAddress']['children']
        ['shipping-address-fieldset'] = $resultShippingAddress;

        return $result;
    }

    public function getBillingFormFields($result)
    {
        $resultBillingAddress = $result;
        if(isset($result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'])
        ) {
            $resultBillingAddress = $result['components']['checkout']['children']['steps']['children']
            ['billing-step']['children']['payment']['children']
            ['payments-list'];
        } else {
            return $result;
        }

        $paymentForms = $resultBillingAddress['children'];

        foreach ($paymentForms as $paymentMethodForm => $paymentMethodValue) {
            $paymentMethodCode = str_replace('-form', '', $paymentMethodForm);

            if (!isset($resultBillingAddress['children'][$paymentMethodCode . '-form'])) {
                continue;
            }

            $billingFields = $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'];
            $customBillingFields = $this->getFields('billingAddress' . $paymentMethodCode . '.custom_attributes','billing');
            $billingFields = array_replace_recursive($billingFields, $customBillingFields);
            $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children'] = $billingFields;

            if (isset($resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['city'])) {
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['city']['visible'] = false;
            }

            if (isset($resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['postcode'])) {
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['postcode']['visible'] = false;
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['postcode']['validation']['required-entry'] = false;
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['postcode']['value'] = '';
            }

            if (isset($resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['country_id'])) {
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['country_id']['sortOrder'] = 90;
            }

            if (isset($resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street'])) {
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street']['sortOrder'] = 106;
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street']['children'][0]['placeholder'] = __('Street');
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street']['children'][1]['placeholder'] = __('Number');
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street']['children'][2]['placeholder'] = __('Complement');
                $resultBillingAddress['children'][$paymentMethodCode . '-form']['children']['form-fields']['children']['street']['children'][1]['validation'] = ['required-entry' => true, 'validate-number' => true];
            }
        }

        $result['components']['checkout']['children']['steps']['children']
        ['billing-step']['children']['payment']['children']
        ['payments-list'] = $resultBillingAddress;

        return $result;
    }

    public function process($result)
    {
        $result = $this->getShippingFormFields($result);
        $result = $this->getBillingFormFields($result);

        if (isset($result['components']['checkoutProvider']['dictionaries'])) {
            $result['components']['checkoutProvider']['dictionaries'] = [
                'country_id' => $result['components']['checkoutProvider']['dictionaries']['country_id'],
                'region_id' => $result['components']['checkoutProvider']['dictionaries']['region_id'],
                'commune_id' => $this->_communeCollection->toOptionArray(),
                'agency_id' => $this->_agencyCollection->toOptionArray()
            ];
        }

        return $result;
    }

    public function getFields($scope, $addressType)
    {
        $fields = [];
        foreach($this->getAdditionalFields($addressType) as $field){
            $fields[$field] = $this->getField($field,$scope);
        }

        return $fields;
    }

    public function getField($attributeCode, $scope)
    {
        $position = 102;
        if ($attributeCode == 'commune_id') {
            $position = 104;
        }
        if ($attributeCode == 'commune') {
            $position = 106;
        }
        if ($attributeCode == 'agency_id') {
            $position = 107;
        }
        if ($attributeCode == 'rut') {
            $position = 108;
        }
        $attributeCodeArray = explode('_', $attributeCode);
        if ($attributeCode != 'commune' && $attributeCode != 'rut') {
            $field = [
                'config' => [
                    'customScope' => $scope,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/select'
                ],
                'component' => 'SoftwareAgil_StarkenPro/js/form/element/' . $attributeCode,
                'dataScope' => $scope . '.' . $attributeCode,
                'sortOrder' => $position,
                'visible' => true,
                'provider' => 'checkoutProvider',
                'validation' => [],
                'options' => [],
                'label' => __(ucfirst($attributeCodeArray[0]))
            ];
        } else {
            $field = [
                'config' => [
                    'customScope' => $scope,
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input'
                ],
                'component' => 'SoftwareAgil_StarkenPro/js/form/element/' . $attributeCode,
                'dataScope' => $scope . '.' . $attributeCode,
                'sortOrder' => $position,
                'visible' => false,
                'provider' => 'checkoutProvider',
                'validation' => [],
                'label' => __(ucfirst($attributeCodeArray[0]))
            ];
            if ($attributeCode == 'rut') {
                $field['visible'] = true;
                $field['label'] = strtoupper($attributeCodeArray[0]) . " (11111111-1)";
                $field['validation'] = ['required-entry' => true, 'validate-cl-rut' => true];
            }
        }
        if ($attributeCode == 'commune_id') {
            $field['validation'] = ['required-entry' => true];
        }

        return $field;
    }

    public function getAdditionalFields($addressType='shipping')
    {
        $shippingAttributes = [];
        $billingAttributes = [];

        $shippingAttributes[] = 'commune_id';
        $billingAttributes[] = 'commune_id';

        $shippingAttributes[] = 'commune';
        $billingAttributes[] = 'commune';

        $shippingAttributes[] = 'rut';
        $billingAttributes[] = 'rut';

        $shippingAttributes[] = 'agency_id';
        $billingAttributes[] = 'agency_id';

        return $addressType == 'shipping' ? $shippingAttributes : $billingAttributes;
    }
}
