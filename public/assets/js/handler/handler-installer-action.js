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

export class HandlerInstallerAction {
    
    /**
     * Processes the save action for the installer configuration.
     * This method updates the values of the JSON input fields based on the provided values, and calls the onSave callback function if it exists in the configuration.
     *
     * @param {Object} values - An object containing the values for the configuration fields.
     * @param {Array} fields - An array of configuration field objects.
     * @param {Object} config - An object containing the configuration for the save action, including the onSave callback function.
     */
    static processSaveAction(values, fields, config) {

        fields.forEach(field => {
            const jsonInput = document.querySelector(`input[name='${field.jsonInputName}']`);
            if (jsonInput) {
                jsonInput.value = values[field.inputSettingName];
                HandlerDOM.iconChange(jsonInput);
            }
        });

        if (config && config.onSave) {
            config.onSave(values);
        }
    }

    /**
     * Processes the add action for the installer configuration.
     * This method creates a new configuration item in the container based on the provided values, fields, and configuration.
     *
     * @param {Object} values - An object containing the values for the configuration fields.
     * @param {Array} fields - An array of configuration field objects.
     * @param {Object} config - An object containing the configuration for the add action, including the target element, container, and edit modal ID.
     */
    static processAddAction(values, fields, config) {
        if (!config || !config.targetElement) return;

        const container = document.getElementById(config.container);
        if (!container) return;

        const uniqueId = Date.now();
        const configType = config.targetElement.replace('json-', '').replace('[]', '');
        //const label = `${configType.charAt(0).toUpperCase() + configType.slice(1)} Configuration`;

        let name = '';
        const configValue = fields.map(field => {
            if (field.password) {
                return '***';
            }
            if ('principal' in field && field.principal) {
                name = values[field.inputSettingName];
            }
            return values[field.inputSettingName];
        }).join(',');

        const newItem = document.createElement('div');
        newItem.className = 'list-group-item d-flex align-items-center';
        newItem.innerHTML = `
            <div class="flex-fill">
                <label for="${configType}-info-${uniqueId}">${config.label}: ${name}</label>
                <div class="text-body text-opacity-60 d-flex align-items-center">
                    <i class="fa fa-circle fs-6px mt-1px fa-fw text-success me-2"></i>
                    <input id="${configType}-info-${uniqueId}" name="${config.targetElement}[]" class="form-control-plaintext text-truncate" type="text" value="${configValue}" readonly/>
                </div>
            </div>
            <div class="w-125px">
                <a href="#${config.editModalId}" data-bs-toggle="modal" class="btn btn-secondary w-125px">
                    <i class="fa-solid fa-pen-to-square"></i> Edit
                </a>
            </div>
        `;

        const passwordFields = fields.filter(field => field.password);
        passwordFields.forEach(field => {
            const passwordInput = document.createElement('input');
            passwordInput.type = 'hidden';
            passwordInput.name = `${config.targetElement.replace('[]', '')}-password[]`;
            passwordInput.value = values[field.inputSettingName];
            newItem.appendChild(passwordInput);
        });

        container.appendChild(newItem);

        if (config && config.onAdd) {
            config.onAdd(values);
        }
    }
}