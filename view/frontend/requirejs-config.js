/**
 * Requirejs Config
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
var config = {
    config: {
        mixins: {
            'Magento_Checkout/js/action/set-billing-address': {
                'SoftwareAgil_StarkenPro/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/set-shipping-information': {
                'SoftwareAgil_StarkenPro/js/action/set-shipping-information-mixin': true
            },
            'Magento_Checkout/js/action/create-shipping-address': {
                'SoftwareAgil_StarkenPro/js/action/create-shipping-address-mixin': true
            },
            'Magento_Checkout/js/action/place-order': {
                'SoftwareAgil_StarkenPro/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/action/create-billing-address': {
                'SoftwareAgil_StarkenPro/js/action/set-billing-address-mixin': true
            },
            'Magento_Checkout/js/model/place-order':{
                'SoftwareAgil_StarkenPro/js/model/place-order-mixin' :true
            },
            'Magento_Checkout/js/model/address-converter':{
                'SoftwareAgil_StarkenPro/js/model/address-converter-mixin' :true
            },
            'Magento_Ui/js/lib/validation/validator': {
                'SoftwareAgil_StarkenPro/js/model/rut-validator-mixin': true
            }
        }
    },
    map: {
        '*': {
            'Magento_Checkout/template/shipping-address/address-renderer/default.html':
                'SoftwareAgil_StarkenPro/template/shipping-address/address-renderer/default.html',
            'Magento_Checkout/template/shipping-information/address-renderer/default.html':
                'SoftwareAgil_StarkenPro/template/shipping-information/address-renderer/default.html',
            'Magento_Checkout/template/billing-address/details.html':
                'SoftwareAgil_StarkenPro/template/billing-address/details.html'
        }
    }
};