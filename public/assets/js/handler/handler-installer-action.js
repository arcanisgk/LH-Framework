import {HandlerDOM} from "./handler-dom.js";

export class HandlerInstallerAction {
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

    static processAddAction(values, config) {
        if (!config || !config.targetElement) return;

        const {targetElement, separator = ',', onAdd} = config;
        const newValue = Object.values(values).join(separator);

        const targetInput = document.querySelector(targetElement);
        if (targetInput) {
            if (targetInput.value) {
                targetInput.value += '\n' + newValue;
            } else {
                targetInput.value = newValue;
            }
        } else {
            HandlerDOM.createNewTextArea(targetElement, newValue);
        }

        if (onAdd) {
            onAdd(values, newValue);
        }
    }
}