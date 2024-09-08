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

export class HandlerDOM {
    static getInputElement(container, inputName) {
        return container.querySelector(`input[name='${inputName}']:not([type='hidden']`) ||
            container.querySelector(`select[name='${inputName}']`);
    }

    static getInputValue(inputElement) {
        return inputElement.type === 'checkbox' ? inputElement.checked : inputElement.value;
    }

    static createNewTextArea(targetSelector, initialValue) {
        const container = document.querySelector(targetSelector);
        if (!container) return;

        const textarea = document.createElement('textarea');
        textarea.value = initialValue;
        textarea.classList.add('form-control');
        container.appendChild(textarea);
    }

    static iconChange(element) {
        let iconElement = element.parentElement.querySelector("i");
        if (iconElement) {
            iconElement.classList.remove("text-body", "text-opacity-25", "text-danger");
            iconElement.classList.add("text-success");
        }
    }

    static showError(errorField) {
        errorField.classList.remove("hide");
        errorField.classList.add("show");
    }

    static hideError(errorField) {
        errorField.classList.remove("show");
        errorField.classList.add("hide");
    }
}