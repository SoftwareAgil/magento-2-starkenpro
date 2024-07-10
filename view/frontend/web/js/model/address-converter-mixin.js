/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
/**
 * @api
 */
define([
    'jquery',
    'mage/utils/wrapper'
], function ($, wrapper) {
    'use strict';

    return function (addressConverter) {
        addressConverter.formAddressDataToQuoteAddress = wrapper.wrapSuper(addressConverter.formAddressDataToQuoteAddress, function (formData) {
            var result = this._super(formData);
            if ($('#shipping-zip-form select[name="commune_id"]').length > 0) {
                if (result.customAttributes === undefined) {
                    result.customAttributes = {};
                }
                result.customAttributes['commune_id'] = $('#shipping-zip-form select[name="commune_id"]').first().val();
                result.customAttributes['commune'] = $('#shipping-zip-form select[name="commune"]').first().val();
                result.customAttributes['rut'] = $('#shipping-zip-form select[name="rut"]').first().val();
                result.customAttributes['agency_id'] = $('#shipping-zip-form select[name="agency_id"]').first().val();
            }
            return result;
        });

        return addressConverter;
    };
});
