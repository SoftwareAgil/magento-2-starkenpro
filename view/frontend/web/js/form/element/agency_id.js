/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'underscore',
    'uiRegistry',
    'Magento_Ui/js/form/element/select',
    'Magento_Checkout/js/model/quote',
    'Magento_Checkout/js/model/shipping-rate-registry',
    'Magento_Checkout/js/model/shipping-rate-processor/new-address'
], function ($, _, registry, Select, quote, rateRegistry, newAddress) {
    'use strict';

    return Select.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.commune_id:value'
            }
        },

        /**
         * Extends instance with defaults, extends config with formatted values
         *     and options, and invokes initialize method of AbstractElement class.
         *     If instance's 'customEntry' property is set to true, calls 'initInput'
         */
        initialize: function () {
            this._super();

            if (this.customEntry) {
                registry.get(this.name, this.initInput.bind(this));
            }

            if (this.filterBy) {
                this.initFilter();
            }

            var agencyOptions = [];
            var jsonObject = {
                value: '',
                title: 'Please select a agency.',
                commune_id: "",
                label: 'Please select a agency.'
            };
            agencyOptions.push(jsonObject);
            this.setOptions(agencyOptions);

            return this;
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var commune = registry.get(this.parentName + '.' + 'commune_id');
            if (typeof commune == 'undefined') return;

            var commune_options = {};
            $.each(commune.initialOptions, function (index, communeOptionValue) {
                if (communeOptionValue.value == value) {
                    commune_options = communeOptionValue;
                }
            });

            var agencyOptions = [];
            var jsonObject = {
                value: '',
                title: 'To send to Agency select an option',
                commune_id: "",
                label: 'To send to Agency select an option'
            };
            agencyOptions.push(jsonObject);

            if (this.initialOptions.length == 0) {
                var checkoutProviderTemp = registry.get('checkoutProvider');
                this.initialOptions = checkoutProviderTemp.dictionaries.agency_id;
            }

            $.each(this.initialOptions, function (index, agencyOptionValue) {
                if (agencyOptionValue.commune_id != commune_options['value']) return;
                var jsonObject = {
                    value: agencyOptionValue.value,
                    title: agencyOptionValue.title,
                    commune_id: agencyOptionValue.commune_id,
                    label: agencyOptionValue.title
                };
                agencyOptions.push(jsonObject);
            });

            this.setOptions(agencyOptions);
        }
    });
});
