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

import {HandlerUtilities} from "../handler-utilities.js";

export class ChoicesPlugin {

    /**
     * Parses a string of options in the format "key1:value1,key2:value2" and returns an object with the key-value pairs.
     * @param {string} options - The string of options to parse.
     * @returns {Object} An object with the parsed key-value pairs.
     */
    getPlOptions(options) {
        return options.split(',').reduce((obj, par) => {
            const [key, value] = par.split(':').map(element => element.trim());
            obj[key] = value;
            return obj;
        }, {});
    }

    /**
     * Renders a Choices.js select element with the specified configuration
     * @param {HTMLElement} element - The select element to enhance with Choices.js
     * @returns {Promise<void>}
     */
    async renderChoices(element) {

        /**
         * Gets the configuration for the Choices instance
         * @param {HTMLElement} target - The target element
         * @returns {Object} Configuration object
         */
        const getChoicesConfig = (target) => ({
            target: target,
            width: target.getAttribute("data-width") || '100%',
            required: target.hasAttribute('required'),
            options: target.getAttribute("data-lh-pl-options") || false
        });

        /**
         * Builds Choices.js options object
         * @param {Object} config - Base configuration object
         * @returns {Object} Complete Choices.js options
         */
        const buildChoicesOptions = (config) => {
            const baseOptions = {
                removeItemButton: true,
                searchEnabled: true,
                searchChoices: true,
                placeholder: true,
                placeholderValue: 'Select an option',
                itemSelectText: '',
                classNames: {
                    containerOuter: 'choices form-select',
                    containerInner: 'choices__inner',
                    input: 'choices__input form-control',
                    inputCloned: 'choices__input--cloned',
                    list: 'choices__list',
                    listItems: 'choices__list--multiple',
                    listSingle: 'choices__list--single',
                    listDropdown: 'choices__list--dropdown',
                    item: 'choices__item',
                    itemSelectable: 'choices__item--selectable',
                    itemDisabled: 'choices__item--disabled',
                    itemChoice: 'choices__item--choice',
                    placeholder: 'choices__placeholder',
                    group: 'choices__group',
                    groupHeading: 'choices__heading',
                    button: 'choices__button'
                }
            };

            const extraOptions = config.options ? this.getPlOptions(config.options) : {};
            const parent = HandlerUtilities.findModalParent(config.target);

            if (extraOptions.size) {
                if (extraOptions.size.toLowerCase() === 'tb') {
                    baseOptions.classNames.containerOuter += ' choices-container--tb';
                }
            }

            if (parent.length > 0 && parent.target) {
                baseOptions.container = parent.target;
            }

            return {...baseOptions, ...extraOptions};
        };

        /**
         * Sets up event handlers for the Choices instance
         * @param {Choices} choicesInstance - The Choices.js instance
         * @param {HTMLElement} nativeSelect - The original select element
         */
        const setupEventHandlers = (choicesInstance, nativeSelect) => {
            choicesInstance.passedElement.element.addEventListener('change', () => {
                nativeSelect.dispatchEvent(new Event('change', {
                    bubbles: true,
                    cancelable: true,
                    composed: true
                }));
            });

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        choicesInstance.refresh();
                    }
                });
            });

            observer.observe(nativeSelect, {
                childList: true,
                subtree: true
            });
        };

        /**
         * Handles required styling for the Choices instance
         * @param {HTMLElement} element - The select element
         */
        const handleRequiredStyling = (element) => {
            if (element.hasAttribute('required')) {
                const choicesElement = element.closest('.choices-outer');
                if (choicesElement) {
                    choicesElement.classList.add('choices-required');
                }
            }
        };

        /**
         * Initializes the Choices instance
         */
        const init = () => {
            const config = getChoicesConfig(element);
            const options = buildChoicesOptions(config);

            // Destroy existing instance if present
            if (element.choices) {
                element.choices.destroy();
            }

            const choicesInstance = new Choices(element, options);
            setupEventHandlers(choicesInstance, config.target);

            if (config.required) {
                handleRequiredStyling(element);
            }
        };

        init();
    }

    /**
     * Initializes the Choices plugin for the given element
     * @param {HTMLElement} element - The element to initialize
     * @returns {Promise<void>}
     */
    async initialize(element) {
        return await this.renderChoices(element);
    }
}
