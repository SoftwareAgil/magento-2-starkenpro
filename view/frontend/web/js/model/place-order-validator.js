/**
 * Place order validator
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define(
    [
        'mage/translate',
        'Magento_Ui/js/model/messageList',
        'Magento_Checkout/js/model/quote',
        'Magento_Checkout/js/checkout-data'
    ],
    function ($t, messageList, quote, checkoutData) {
        'use strict';
        return {
            validate: function () {
                var isValid = true;

                var selectedShippingRate = checkoutData.getSelectedShippingRate();
                if (!selectedShippingRate) {
                    return isValid;
                }
                if (selectedShippingRate.indexOf("starkenpro") == -1) {
                    return isValid;
                }

                var address = quote.shippingAddress();
                var shipCommuneId = '';
                var shipAgencyId = '';
                if (typeof address.customAttributes.length != 'undefined') {
                    for (var i = 0; i < address.customAttributes.length; i++) {
                        if (address.customAttributes[i].attribute_code == 'commune_id') {
                            shipCommuneId = address.customAttributes[i].value;
                        }
                        if (address.customAttributes[i].attribute_code == 'agency_id') {
                            shipAgencyId = address.customAttributes[i].value;
                        }
                    }
                } else if (typeof address.customAttributes.commune_id.value != 'undefined') {
                    shipCommuneId = address.customAttributes.commune_id.value;
                    shipAgencyId = '';
                    if (typeof address.customAttributes.agency_id != 'undefined') {
                        shipAgencyId = address.customAttributes.agency_id.value;
                    }
                } else {
                    shipCommuneId = address.customAttributes.commune_id;
                    shipAgencyId = '';
                    if (typeof address.customAttributes.agency_id != 'undefined') {
                        shipAgencyId = address.customAttributes.agency_id;
                    }
                }

                if (selectedShippingRate.indexOf("_agencia_") != -1 && shipCommuneId != '' && (shipAgencyId == '' || shipAgencyId == '0')) {
                    messageList.addErrorMessage({ message: $t('The domicile agency is mandatory in the shipping address, for the selected shipping rate. Please add it in shipping step (previous step) or in addresses book if it is a saved address.') });
                    isValid = false;
                }

                return isValid;
            }
        }
    }
);
