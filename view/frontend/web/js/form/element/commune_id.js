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
                update: '${ $.parentName }.region_id:value'
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

            var communeOptions = [];
            var jsonObject = {
                value: '',
                title: 'Please select a commune.',
                region_id: "",
                label: 'Please select a commune.'
            };
            communeOptions.push(jsonObject);
            this.setOptions(communeOptions);

            return this;
        },

        /**
         * @param {String} value
         */
        update: function (value) {
            var region = registry.get(this.parentName + '.' + 'region_id');
            if (typeof region == 'undefined') return;

            var region_options = {};
            $.each(region.initialOptions, function (index, regionOptionValue) {
                if (regionOptionValue.value == value) {
                    region_options = regionOptionValue;
                }
            });

            var communeOptions = [];
            var jsonObject = {
                value: '',
                title: 'Please select a commune.',
                region_id: "",
                label: 'Please select a commune.'
            };
            communeOptions.push(jsonObject);

            if (this.initialOptions.length == 0) {
                var checkoutProviderTemp = registry.get('checkoutProvider');
                this.initialOptions = checkoutProviderTemp.dictionaries.commune_id;
            }

            $.each(this.initialOptions, function (index, communeOptionValue) {
                if (communeOptionValue.region_id != region_options['value']) return;
                var jsonObject = {
                    value: communeOptionValue.value,
                    title: communeOptionValue.title,
                    region_id: communeOptionValue.region_id,
                    label: communeOptionValue.title
                };
                communeOptions.push(jsonObject);
            });

            if (this.parentName.indexOf('shipping-step') >= 0) {
                $('.form-shipping-address input[name="region"]').val(region_options['label']).change();
            } else if (this.parentName.indexOf('billing') >= 0) {
                $('.billing-address-form input[name="region"]').val(region_options['label']).change();
            }

            this.setOptions(communeOptions);
        },

        /**
         * Callback that fires when 'value' property is updated.
         */
        onUpdate: function () {
            this.bubble('update', this.hasChanged());

            if ($('#shipping-zip-form select[name="commune_id"]').length > 0 &&  $('#shipping-zip-form select[name="commune_id"]').val() != '') {
                var address = quote.shippingAddress();
                if (address.customAttributes === undefined) {
                    address.customAttributes = {};
                }
                address.customAttributes['commune_id'] = this.value();
                rateRegistry.set(address.getKey(), null);
                rateRegistry.set(address.getCacheKey(), null);
                newAddress.getRates(address);
            }

            this.validate();
        }
    });
});
