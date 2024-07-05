/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

/**
 * @api
 */
define([
    'jquery',
    'uiRegistry',
    'Magento_Ui/js/form/element/abstract'
], function ($, registry, Abstract) {
    'use strict';

    return Abstract.extend({
        defaults: {
            skipValidation: false,
            imports: {
                update: '${ $.parentName }.commune_id:value'
            }
        },

        /**
         * On value change handler.
         *
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

            if (commune_options.length == 0) {
                return;
            }

            if (this.parentName.indexOf('shipping-step') >= 0) {
                $('.form-shipping-address input[name="custom_attributes[commune]"]').val(commune_options['label']).change();
                $('.form-shipping-address input[name="city"]').val(commune_options['label']).change();
            } else if (this.parentName.indexOf('billing') >= 0) {
                $('.billing-address-form input[name="custom_attributes[commune]"]').val(commune_options['label']).change();
                $('.billing-address-form input[name="city"]').val(commune_options['label']).change();
            }
        },
    });
});
