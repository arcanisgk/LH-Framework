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


/**
 * Displays error messages for a given set of errors.
 *
 * @param {Array<{field: string, status: string, smg: string}>} errors - An array of error objects, each containing the field, status, and error message.
 * @returns {Promise<void>} - A Promise that resolves when the error messages have been displayed.
 */
export class HandlerError {

    /**
     * @param {Array<{field: string, status: string, smg: string}>} errors
     */
    static async displayError(errors) {
        console.log(errors);
        errors.forEach(error => {

            const errorContainer = document.getElementById(error.field);

            if (errorContainer && error.status === 'error') {
                if (error.smg.includes('invalid-feedback')) {
                    const inputFieldId = error.field.replace('-smg', '');
                    const inputElement = document.getElementById(inputFieldId);

                    if (inputElement) {
                        inputElement.classList.add('is-invalid');
                        errorContainer.className = 'invalid-feedback blink';
                        errorContainer.innerHTML = error.smg
                            .replace('<div class="invalid-feedback">', '')
                            .replace('</div>', '')
                            .trim();
                    }
                } else {

                    HandlerDOM.showContent(errorContainer);
                    errorContainer.innerHTML = error.smg;
                }
            }
        });
    }
}