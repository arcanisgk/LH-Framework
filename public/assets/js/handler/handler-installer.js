import {HandlerConsoleOutput} from "./handler-console-output.js";
import {HandlerModal} from "./handler-modal.js";
import {HandlerValidation} from "./handler-validation.js";
import {HandlerDOM} from "./handler-dom.js";
import {HandlerInstallerAction} from "./handler-installer-action.js";

export class HandlerInstaller {

    constructor() {
        this.isSetup = (window.location.pathname === '/setup');
        this.output = new HandlerConsoleOutput();
        this.handlerModal = new HandlerModal();
        this.modalids = [
            'modal-company-name',
            'modal-company-owner',
            'modal-project-name',
            'modal-license',
            'modal-lang',
            'modal-entry-view',
            'modal-session-life-time',
            'modal-session-activity-expire',
            'modal-add-email',
            'modal-add-ftp',
            'modal-add-database',
        ];
    }

    setProtocol() {
        const inputProtocol = document.querySelector('input[name="json-protocol"]');
        inputProtocol.value = window.location.protocol.replace(':', '');
    }

    setDomain() {
        const domain = window.location.hostname;
        const inputDomain = document.querySelector('input[name="json-domain"]');
        const inputSessionName = document.querySelector('input[name="json-session-name"]');

        inputDomain.value = domain;
        inputSessionName.value = `${domain.split('.').slice(0, -1).join('.')}-session`;
    }

    closeBtnAlertEvent() {
        document.querySelectorAll(".btn-close").forEach(button => {
            button.addEventListener("click", function () {
                const alert = this.closest(".alert");
                if (alert) {
                    alert.classList.replace("show", "hide");
                }
            });
        });
    }

    validateInputs = (inputs, stepNumber) => {
        const allValid = inputs.every(input =>
            document.querySelector(`input[name='${input}']`).value !== "Click on Edit"
        );

        if (allValid) {
            document.querySelector(`input[name='step-${stepNumber}']`).value = true;
            document.getElementById(`step-${stepNumber}-text`).classList.remove("hide");
        }

        return allValid;
    };

    validateStep1 = () => this.validateInputs([
        'json-company-name',
        'json-company-owner',
        'json-project-name',
        'json-license',
        'json-protocol',
        'json-domain',
        'json-lang',
        'json-entry'
    ], 1);


    getFieldValues(modal, fields) {
        return fields.reduce((acc, field) => {
            const inputElement = HandlerDOM.getInputElement(modal, field.inputSettingName);
            acc[field.inputSettingName] = HandlerDOM.getInputValue(inputElement);
            return acc;
        }, {});
    }

    validateFields(modal, fields, values) {
        return fields.every(field => {
            const value = values[field.inputSettingName];
            const errorField = document.getElementById(field.errorFieldId);

            if (HandlerValidation.validateField(value, field.rules)) {
                if (errorField) HandlerDOM.hideError(errorField);
                return true;
            } else {
                if (errorField) HandlerDOM.showError(errorField);
                return false;
            }
        });
    }

    handleButtonClick(action, modal, fields, modalId, validateStep, actionConfig) {

        const values = this.getFieldValues(modal, fields);
        const allValid = this.validateFields(modal, fields, values);

        if (allValid) {
            if (action === 'save') {
                HandlerInstallerAction.processSaveAction(values, fields, actionConfig);
            } else if (action === 'add') {
                HandlerInstallerAction.processAddAction(values, actionConfig);
            }
            this.handlerModal.hideModal(modalId);
        }

        if (validateStep) this[validateStep]();
    }

    setupButtonListener(modal, buttonPrefix, action, fields, modalId, validateStep, actionConfig) {
        const button = modal.querySelector(`button[name^="${buttonPrefix}"]`);
        if (button) {
            button.addEventListener("click", () => this.handleButtonClick(action, modal, fields, modalId, validateStep, actionConfig));
        }
    }

    setupCheckboxListener(modal, field) {

        const checkbox = modal.querySelector(`input[name='${field.inputSettingName}'][type="checkbox"]`);
        const input = modal.querySelector(`input[name='${field.relatedEvent.input}']`);

        if (checkbox && input) {

            checkbox.addEventListener('change', () => {
                console.log('clicked');
                input.readOnly = checkbox.checked;
                if (checkbox.checked && field.relatedEvent.value) {
                    input.value = field.relatedEvent.value;
                }
            });
        }
    }

    setupModalListener(config) {
        const {modalId, fields, validateStep, saveConfig = null, addConfig = null} = config;

        const modal = document.getElementById(modalId);

        if (modal) {
            this.setupButtonListener(modal, 'b-save-', 'save', fields, modalId, validateStep, saveConfig);
            this.setupButtonListener(modal, 'b-add-', 'add', fields, modalId, validateStep, addConfig);

            fields.forEach(field => {
                if (field.checkbox && field.relatedEvent) {
                    this.setupCheckboxListener(modal, field);
                }
            });
        } else {
            HandlerConsoleOutput.defaultMGS('error', `Modal element not found: ${modalId}`, error);
        }
    }

    setupEventListeners() {

        this.setupModalListener({
            modalId: 'modal-company-name',
            fields: [{
                inputSettingName: 'i-company-name',
                jsonInputName: 'json-company-name',
                errorFieldId: 'a-company-name',
                rules: {required: true, lengthLess: 8}
            }],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Company name saved:', values['i-company-name']);
                }
            }
        });

        this.setupModalListener({
            modalId: 'modal-company-owner',
            fields: [{
                inputSettingName: 'i-company-owner',
                jsonInputName: 'json-company-owner',
                errorFieldId: 'a-company-owner',
                rules: {required: true, lengthLess: 8}
            }],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Company Owner saved:', values['i-company-owner']);
                }
            }
        });

        this.setupModalListener({
            modalId: 'modal-project-name',
            fields: [{
                inputSettingName: 'i-project-name',
                jsonInputName: 'json-project-name',
                errorFieldId: 'a-project-name',
                rules: {required: true, lengthLess: 4}
            }],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Project name saved:', values['i-project-name']);
                }
            }
        });

        this.setupModalListener({
            modalId: 'modal-license',
            fields: [{
                inputSettingName: 'i-license',
                jsonInputName: 'json-license',
                errorFieldId: 'a-license',
                rules: {required: true, length: 19, pattern: /^[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}-[A-Z0-9]{4}$/}
            }, {
                inputSettingName: 'chk-license',
                jsonInputName: 'json-chk-license',
                checkbox: true,
                relatedEvent: {input: 'i-license', value: 'FREE-FREE-FREE-FREE'},
            }],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Company name saved:', values['i-license']);
                }
            }
        });

        this.setupModalListener({
            modalId: 'modal-lang',
            fields: [{
                inputSettingName: 'i-lang',
                jsonInputName: 'json-lang',
            }, {
                inputSettingName: 'multi-language',
                jsonInputName: 'json-m-lang',
            }],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Language saved:', values['i-lang']);
                }
            }
        });

        this.setupModalListener({
            modalId: 'modal-entry-view',
            fields: [{inputSettingName: 'i-entry', jsonInputName: 'json-entry'}],
            validateStep: 'validateStep1',
            saveConfig: {
                onSave: (values) => {
                    console.log('Entry Point saved:', values['i-entry']);
                }
            }
        });

    }

    async Installer() {
        this.setProtocol();
        this.setDomain();
        this.closeBtnAlertEvent();
        this.handlerModal.initializeModals(this.modalids);
        this.setupEventListeners();
    }

    async Init() {
        if (!this.isSetup) return;

        try {
            await this.output.defaultMGS('loader', 'Installer Assets');
            await this.Installer();
            await this.output.defaultMGS('end-loader', 'Installer Assets');
        } catch (error) {
            await HandlerConsoleOutput.defaultMGS('error', 'Installer Interfaces', error);
        }
    }

}