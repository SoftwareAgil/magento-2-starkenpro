/**
 * Shipping rates JS validator
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define([
    'jquery',
    'mageUtils',
    './shipping-rates-validation-rules',
    'mage/translate'
], function ($, utils, validationRules, $t) {
    'use strict';

    var checkoutConfig = window.checkoutConfig;

    return {
        validationErrors: [],

        /**
         * @param {Object} address
         * @return {Boolean}
         */
        validate: function (address) {
            var rules = validationRules.getRules(),
                self = this;

            $.each(rules, function (field, rule) {
                var message;

                if (rule.required && utils.isEmpty(address[field])) {
                    message = $t('Field ') + field + $t(' is required.');
                    self.validationErrors.push(message);
                }
            });

            if (!this.validationErrors.length) {
                if (address['country_id'] == checkoutConfig.originCountryCode) {
                    return !utils.isEmpty(address.postcode);
                }

                return true;
            }

            return false;
        }
    };
});
