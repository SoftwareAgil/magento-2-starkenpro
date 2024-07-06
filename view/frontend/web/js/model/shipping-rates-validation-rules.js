/**
 * Shipping rates JS validations rules
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define([], function () {
    'use strict';

    return {
        /**
         * @return {Object}
         */
        getRules: function () {
            return {
                'country_id': {
                    'required': true
                },
                'commune_id': {
                    'required': true
                },
                'commune': {
                    'required': false
                },
                'agency_id': {
                    'required': false
                },
                'postcode': {
                    'required': false
                }
            };
        }
    };
});
