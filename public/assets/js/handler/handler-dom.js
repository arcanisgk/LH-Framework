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


/**
 * Provides a set of utility methods for working with DOM elements.
 */
export class HandlerDOM {

    /**
     * Finds an input or select element within the given container that has the specified name.
     *
     * @param {HTMLElement} container - The container element to search within.
     * @param {string} inputName - The name of the input or select element to find.
     * @returns {HTMLElement|null} The found input or select element, or null if not found.
     */
    static getInputElement(container, inputName) {
        return container.querySelector(`input[name='${inputName}']:not([type='hidden']`) ||
            container.querySelector(`select[name='${inputName}']`);
    }

    /**
     * Gets the value of the given input element, handling the case of checkboxes separately.
     *
     * @param {HTMLInputElement} inputElement - The input element to get the value from.
     * @returns {string|boolean} The value of the input element. For checkboxes, it returns the boolean checked state.
     */
    static getInputValue(inputElement) {
        return inputElement.type === 'checkbox' ? inputElement.checked : inputElement.value;
    }

    /**
     * Creates a new textarea element with the specified initial value and adds it to the target container.
     *
     * @param {string} targetSelector - The CSS selector for the container element to append the textarea to.
     * @param {string} initialValue - The initial value to set for the textarea.
     */
    static createNewTextArea(targetSelector, initialValue) {
        const container = document.querySelector(targetSelector);
        if (!container) return;

        const textarea = document.createElement('textarea');
        textarea.value = initialValue;
        textarea.classList.add('form-control');
        container.appendChild(textarea);
    }

    /**
     * Changes the CSS classes of an icon element to indicate a successful state.
     *
     * @param {HTMLElement} element - The parent element of the icon element to be updated.
     */
    static iconChange(element) {
        let iconElement = element.parentElement.querySelector("i");
        if (iconElement) {
            iconElement.classList.remove("text-body", "text-opacity-25", "text-danger");
            iconElement.classList.add("text-success");
        }
    }

    /**
     * Shows the content of an error field by removing the "hide" and "d-none" classes and adding the "show" class.
     *
     * @param {HTMLElement} errorField - The error field element to show.
     */
    static showContent(errorField) {
        errorField.classList.remove("hide", "d-none");
        errorField.classList.add("show");
    }

    /**
     * Hides the content of an error field by removing the "show" class and adding the "hide" and "d-none" classes.
     *
     * @param {HTMLElement} errorField - The error field element to hide.
     */
    static hideContent(errorField) {
        errorField.classList.remove("show");
        errorField.classList.add("hide", "d-none");
    }
}