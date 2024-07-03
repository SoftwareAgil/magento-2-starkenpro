/**
 * Shipping rates view validations
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define([
    'uiComponent',
    'Magento_Checkout/js/model/shipping-rates-validator',
    'Magento_Checkout/js/model/shipping-rates-validation-rules',
    '../model/shipping-rates-validator',
    '../model/shipping-rates-validation-rules'
], function (
    Component,
    defaultShippingRatesValidator,
    defaultShippingRatesValidationRules,
    spShippingRatesValidator,
    spShippingRatesValidationRules
) {
    'use strict';

    defaultShippingRatesValidator.registerValidator('starkenpro', spShippingRatesValidator);
    defaultShippingRatesValidationRules.registerRules('starkenpro', spShippingRatesValidationRules);

    return Component;
});
