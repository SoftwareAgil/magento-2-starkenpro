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
            skipValidation: false
        }
    });
});
