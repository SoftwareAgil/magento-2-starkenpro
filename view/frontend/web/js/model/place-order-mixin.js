/**
 * Custom attributes in place order model
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define([
    'jquery',
    'mage/utils/wrapper',
    'Magento_Checkout/js/model/full-screen-loader'
], function (
    $,
    wrapper,
    fullScreenLoader
) {
    'use strict';

    return function (placeOrderAction) {
        return wrapper.wrap(placeOrderAction, function (originalAction, serviceUrl, paymentData, messageContainer) {
            return originalAction(serviceUrl, paymentData, messageContainer).fail(
                function () {
                    fullScreenLoader.stopLoader();
                }
            );
        });
    };
});
