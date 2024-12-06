/**
 * Last Hammer Framework 2.0
 * JavaScript Version (ES6+).
 *
 * @see https://github.com/arcanisgk/LH-Framework
 *
 * @author    Walter Nuñez (arcanisgk/founder) <icarosnet@gmail.com>
 * @copyright 2017 - 2024
 * @license   http://www.gnu.org/copyleft/lesser.html GNU Lesser General Public License
 * @note      This program is distributed in the hope that it will be useful
 * WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 * or FITNESS FOR A PARTICULAR PURPOSE.
 */

import {HandlerDOM} from "./handler-dom.js";

export class HandlerValidation {

    /**
     * Validates a single field based on the provided rules.
     *
     * @param {*} value - The value of the field to be validated.
     * @param {Object} rules - An object containing the validation rules for the field.
     * @param {Object} allValues - An object containing the current values for all fields.
     * @returns {boolean} - `true` if the field is valid, `false` otherwise.
     */
    static validateField(value, rules, allValues) {
        if (!rules) return true;

        const {required = false, length = null, lengthLess = null, pattern = null, comparison = null} = rules;

        if (value === undefined || value === null) return false;
        if (required && (typeof value === 'string' && value.trim() === "")) return false;
        if (length !== null && value.length !== length) return false;
        if (lengthLess !== null && value.length <= lengthLess) return false;
        if (pattern !== null && !pattern.test(value)) return false;

        if (comparison && rules.input) {
            switch (comparison) {
                case 'identical':
                    let ideValue = allValues[rules.input];
                    if (value !== ideValue) return false;
                    break;
                case 'similar': //case un-sensitive
                    let simValue = allValues[rules.input];
                    if (value.toLowerCase() !== simValue.toLowerCase()) return false;
                    break;
                default:
                    break;
            }
        }
        return true;
    }

    /**
     * Validates all fields in the provided `fields` array using the corresponding validation rules.
     *
     * @param {Object[]} fields - An array of field objects, each containing an `inputSettingName` and `errorFieldId` property, as well as a `rules` object with validation rules.
     * @param {Object} values - An object containing the current values for all fields.
     * @returns {boolean} - `true` if all fields are valid, `false` otherwise.
     */
    static validateAllFields(fields, values) {
        return fields.every(field => {
            const value = values[field.inputSettingName];
            const errorField = document.getElementById(field.errorFieldId);

            const isValid = this.validateField(value, field.rules, values);

            if (isValid) {
                if (errorField) HandlerDOM.hideContent(errorField);
                return true;
            } else {
                if (errorField) HandlerDOM.showContent(errorField);
                return false;
            }

        });
    }
}