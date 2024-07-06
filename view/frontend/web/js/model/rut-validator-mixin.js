/**
 * Validator mixin - RUT validation rule
 *
 * @category     SoftwareAgil
 * @package      SoftwareAgil_StarkenPro
 * @author       SoftwareAgil (info@softwareagil.com)
 * @copyright    Copyright (c) 2020 SoftwareAgil (www.softwareagil.com)
 */
define([
    'jquery'
], function($) {
    return function(validator) {
        validator.addRule(
            'validate-cl-rut',
            function (value) {
                if (value.trim() == '') {
                    return false;
                }
                var valueArray = value.split('-');
                if (valueArray.length <= 1) {
                    return false;
                }
                if (isNaN(valueArray[0]) || valueArray[1].trim() == '') {
                    return false;
                }
                if (valueArray[0].length != 8 || valueArray[1].length != 1) {
                    return false;
                }
                var mainNumber = valueArray[0].trim();
                var dvNumber = valueArray[1].trim();
                var mainNumberArray = mainNumber.split();
                var M=0,S=1, T = mainNumber;
                for(;T;T=Math.floor(T/10)) {
                    S=(S+T%10*(9-M++%6))%11;
                }
                var dvNumberNew = S?S-1:'k';
                if (dvNumberNew != dvNumber) {
                    return false;
                }
                return true;
            },
            $.mage.__('Invalid RUT format')
        );
        return validator;
    }
});