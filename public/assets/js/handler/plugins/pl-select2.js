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

export class Select2Plugin {
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

    async renderSelect2(element) {

        const getSelectConfig = (target) => ({
            target: $(target),
            width: target.getAttribute("data-width") || '100px',
            required: target.hasAttribute('required'),
            options: target.getAttribute("data-lh-pl-options") || false
        });

        const buildSelect2Options = (config) => {

            const baseOptions = {
                width: config.width,
                dropdownAutoWidth: true,
            };

            const extraOptions = config.options ? this.getPlOptions(config.options) : {};
            const parent = HandlerUtilities.findModalParent(config.target);

            if (extraOptions.size) {
                if (extraOptions.size.toLowerCase() === 'tb') {
                    baseOptions.containerCssClass = 'select2-container--tb';
                    baseOptions.dropdownCssClass = 'select2-dropdown--tb';
                }
            }

            if (parent.length > 0 && parent.target) {
                baseOptions.dropdownParent = parent.target;
            }

            return {...baseOptions, ...extraOptions};
        };

        const setupEventHandlers = (select) => {

            const nativeSelect = select[0];

            select.on('select2:select', function (e) {
                nativeSelect.value = e.params.data.id;
                nativeSelect.dispatchEvent(new Event('change', {
                    bubbles: true,
                    cancelable: true,
                    composed: true
                }));
            });

            const observer = new MutationObserver((mutations) => {
                mutations.forEach((mutation) => {
                    if (mutation.type === 'childList') {
                        select.trigger('change');
                    }
                });
            });

            observer.observe(nativeSelect, {
                childList: true,
                subtree: true
            });

        };

        const handleRequiredStyling = (select) => {
            select.next().children().children().each(function () {
                $(this).css("border-color", "#f8ac59");
            });
        };

        const init = () => {
            const config = getSelectConfig(element);
            const options = buildSelect2Options(config);

            if (config.target.hasClass('select2-hidden-accessible')) {
                config.target.select2('destroy');
            }
            config.target.select2(options);
            setupEventHandlers(config.target, config);

            if (config.required) {
                handleRequiredStyling(config.target);
            }
        };

        init();

    }

    async initialize(element) {
        return await this.renderSelect2(element);
    }
}