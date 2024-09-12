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
import {HandlerConsoleOutput} from "../../handler/handler-console-output.js";
import {HandlerModal} from "../../handler/handler-modal.js";
import {HandlerDOM} from "../../handler/handler-dom.js";
import {HandlerValidation} from "../../handler/handler-validation.js";
import {HandlerInstallerAction} from "../../handler/handler-installer-action.js";
import {HandlerAudio} from "../../handler/handler-audio.js";
import {HandlerRequest} from "../../handler/handler-request.js";


export class HandlerInstaller {

    /**
     * HandlerInstaller constructor.
     *
     * @constructor
     */
    constructor() {
        this.isSetup = (window.location.pathname === '/Setup');
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

    async showWelcome() {
        const defaultModal = new bootstrap.Modal(document.getElementById('welcome'));
        defaultModal.show();
    }

    /**
     * Set the protocol field value.
     *
     * @async
     * @returns {Promise<void>}
     */
    async setProtocol() {
        const inputProtocol = document.querySelector('input[name="json-protocol"]');
        inputProtocol.value = window.location.protocol.replace(':', '');
    }

    /**
     * Set the domain field value and generate a session name.
     *
     * @async
     * @returns {Promise<void>}
     */
    async setDomain() {
        const domain = window.location.hostname;
        const inputDomain = document.querySelector('input[name="json-domain"]');
        const inputSessionName = document.querySelector('input[name="json-session-name"]');

        inputDomain.value = domain;
        inputSessionName.value = `${domain.split('.').slice(0, -1).join('.')}-session`;
    }


    async closeBtnAlertEvent() {
        document.querySelectorAll(".btn-close").forEach(button => {
            button.addEventListener("click", () => {
                const alert = button.closest(".alert");
                if (alert) {
                    alert.classList.replace("show", "hide");
                }
            });
        });
    }


    async validateInputs(inputs, stepNumber) {

        const allValid = inputs.every(input => document.querySelector(`input[name='${input}']`).value !== "Click on Edit")

        if (allValid) {
            document.querySelector(`input[name='step-${stepNumber}']`).value = true;
            document.getElementById(`step-${stepNumber}-text`).classList.remove("hide");
        }

        return allValid;
    };


    async validateStepGeneralInfo() {
        return this.validateInputs([
            'json-company-name',
            'json-company-owner',
            'json-project-name',
            'json-license',
            'json-protocol',
            'json-domain',
            'json-lang',
            'json-entry'
        ], 1);
    }


    async validateStepSession() {
        return this.validateInputs([
            'json-session-lifetime-hours',
            'json-session-lifetime-days',
            'json-session-activity-expire-hours',
            'json-session-activity-expire-days'
        ], 2);
    }


    async validateEmailConfig() {
        return this.validateInputs(['json-mail-conf[]'], 3);
    }

    async validateFTPConfig() {
        return this.validateInputs(['json-ftp-conf[]'], 4);
    }

    async validateDatabaseConfig() {
        return this.validateInputs(['json-database-conf[]'], 5);
    }


    async getFieldValues(modal, fields) {
        return fields.reduce((acc, field) => {
            const inputElement = HandlerDOM.getInputElement(modal, field.inputSettingName);
            let value = HandlerDOM.getInputValue(inputElement);
            if (value === '' && field.default !== null) value = field.default;
            acc[field.inputSettingName] = value;
            return acc;
        }, {});
    }


    async handleButtonClick(action, modal, fields, modalId, validateStep, actionConfig) {

        const values = await this.getFieldValues(modal, fields);
        const allValid = await HandlerValidation.validateAllFields(fields, values);

        if (allValid) {
            if (action === 'save') {
                await HandlerInstallerAction.processSaveAction(values, fields, actionConfig);
            } else if (action === 'add') {
                await HandlerInstallerAction.processAddAction(values, fields, actionConfig);
            }
            if (validateStep) await this[validateStep]();
            this.handlerModal.hideModal(modalId);
        }
    }


    async setupButtonListener(modal, buttonPrefix, action, fields, modalId, validateStep, actionConfig) {
        const button = modal.querySelector(`button[name^="${buttonPrefix}"]`);
        if (button) {
            button.addEventListener("click", () => this.handleButtonClick(action, modal, fields, modalId, validateStep, actionConfig));
        }
    }


    async setupCheckboxListener(modal, field) {

        const checkbox = modal.querySelector(`input[name='${field.inputSettingName}'][type="checkbox"]`);
        const input = modal.querySelector(`input[name='${field.relatedEvent.input}']`);

        if (checkbox && input) {
            checkbox.addEventListener('change', () => {
                input.readOnly = checkbox.checked;
                if (checkbox.checked && field.relatedEvent.value) {
                    input.value = field.relatedEvent.value;
                }
            });
        }
    }


    async setupPasswordListener(modal, field, fields) {
        const inputElement = HandlerDOM.getInputElement(modal, field.inputSettingName);
        const relatedElement = HandlerDOM.getInputElement(modal, field.rules.input);

        if (inputElement && relatedElement) {
            inputElement.addEventListener('input', () => this.validateRelatedFields(inputElement, relatedElement, field));
            relatedElement.addEventListener('input', () => this.validateRelatedFields(relatedElement, inputElement, fields.find(f => f.inputSettingName === field.rules.input)));
        }
    }


    async setupModalListener(config) {
        const {modalId, fields, validateStep, saveConfig = null, addConfig = null} = config;

        const modal = document.getElementById(modalId);

        try {
            if (modal) {

                if (saveConfig) {
                    await this.setupButtonListener(modal, 'b-save-', 'save', fields, modalId, validateStep, saveConfig);
                }

                if (addConfig) {
                    await this.setupButtonListener(modal, 'b-add-', 'add', fields, modalId, validateStep, addConfig);
                }

                fields.forEach(field => {
                    if (field.checkbox && field.relatedEvent) {
                        this.setupCheckboxListener(modal, field);
                    }

                    if (field.password && field.rules && field.rules.input) {
                        this.setupPasswordListener(modal, field, fields);
                    }
                });

            } else {

                throw new Error(`Modal element not found: ${modalId}`);

            }
        } catch (e) {
            await HandlerConsoleOutput.defaultMGS('error', e.message);
        }


    }

    validateRelatedFields(inputElement, relatedElement, fieldConfig) {
        const errorField = document.getElementById(fieldConfig.errorFieldId);
        const isValid = HandlerValidation.validateField(inputElement.value, fieldConfig.rules, {[relatedElement.name]: relatedElement.value});

        if (isValid) {
            HandlerDOM.hideError(errorField);
        } else {
            HandlerDOM.showError(errorField);
        }
    }


    async saveJson() {
        const inputElements = document.querySelectorAll('input[name^="json-"]');
        const form_data = new FormData();
        let error = false;
        let errorField = [];
        inputElements.forEach(input => {
            let fieldName = input.name;
            let value = input.value;
            if (value === 'Click on Edit') {
                error = true;
                let labelFor = document.querySelector('label[for="' + input.id + '"]');
                if (labelFor !== null && labelFor.textContent !== '') {
                    errorField.push(labelFor.textContent);
                    const inputTarget = document.querySelector("input[name='" + fieldName + "']");
                    HandlerDOM.iconChange(inputTarget);
                }
            }
            form_data.append(fieldName, value);
        });
        if (!error) {

            //uri

            await HandlerRequest.request({
                uri: window.location.href,
                data: form_data,
                type: 'multipart/form-data',
                method: 'post',
                response: 'Swal',
                error: 'Swal'
            });

            /*
            axios.post(window.location.href, form_data, {
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            }).then(async response => {
                if (response.data) {
                    await HandlerAudio.playAudio('finish-install');
                    Swal.fire({
                        icon: 'success',
                        html: 'Installation completed successfully!!!',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Return to Form'
                    }).then(() => window.location.href = window.location.origin);
                } else {
                    Swal.fire({
                        icon: 'warning',
                        html: 'Installation error!!!',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Return to Form'
                    })
                    console.log(response.error);
                }
            }).catch(error => {
                console.error(error);
            });
            */


        } else {
            console.log({errorField});
            Swal.fire({
                icon: 'error',
                html: '<div class="text-start">For the platform to work correctly, you must fill out all the fields, otherwise you will not be able to continue.<br><br><b>Fields:</b><br>' + errorField.join("<br>") + '</div>',
                showCancelButton: true,
                showConfirmButton: false,
                cancelButtonText: 'Return to Form'
            })
        }
    }

    async setupSaveJsonListener() {
        const saveChangesJsonSave = document.querySelector("button[name='b-save-json']");
        saveChangesJsonSave.addEventListener("click", async () => await this.saveJson());
    }


    async setupEventListeners() {

        await this.setupModalListener({
            modalId: 'modal-company-name',
            fields: [{
                inputSettingName: 'i-company-name',
                jsonInputName: 'json-company-name',
                errorFieldId: 'a-company-name',
                rules: {required: true, lengthLess: 8}
            }],
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('Company name saved:', values['i-company-name']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-company-owner',
            fields: [{
                inputSettingName: 'i-company-owner',
                jsonInputName: 'json-company-owner',
                errorFieldId: 'a-company-owner',
                rules: {required: true, lengthLess: 8}
            }],
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('Company Owner saved:', values['i-company-owner']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-project-name',
            fields: [{
                inputSettingName: 'i-project-name',
                jsonInputName: 'json-project-name',
                errorFieldId: 'a-project-name',
                rules: {required: true, lengthLess: 4}
            }],
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('Project name saved:', values['i-project-name']);
                }
            }
        });

        await this.setupModalListener({
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
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('License Software saved:', values['i-license']);
                    console.log('License Software for FREE:', values['chk-license']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-lang',
            fields: [{
                inputSettingName: 'i-lang',
                jsonInputName: 'json-lang',
            }, {
                inputSettingName: 'multi-language',
                jsonInputName: 'json-m-lang',
            }],
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('Language saved:', values['i-lang']);
                    console.log('Multi-Language Support:', values['multi-language']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-entry-view',
            fields: [{inputSettingName: 'i-entry', jsonInputName: 'json-entry'}],
            validateStep: 'validateStepGeneralInfo',
            saveConfig: {
                onSave: (values) => {
                    console.log('Entry Point saved:', values['i-entry']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-session-life-time',
            fields: [
                {
                    inputSettingName: 'i-session-lifetime-hours',
                    jsonInputName: 'json-session-lifetime-hours',
                    default: 0
                },
                {
                    inputSettingName: 'i-session-lifetime-days',
                    jsonInputName: 'json-session-lifetime-days',
                    default: 0
                },
            ],
            validateStep: 'validateStepSession',
            saveConfig: {
                onSave: (values) => {
                    console.log('Session Lifetime Days:', values['i-session-lifetime-days']);
                    console.log('Session Lifetime Hours:', values['i-session-lifetime-hours']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-session-activity-expire',
            fields: [
                {
                    inputSettingName: 'i-session-activity-expire-hours',
                    jsonInputName: 'json-session-activity-expire-hours',
                    default: 0
                },
                {
                    inputSettingName: 'i-session-activity-expire-days',
                    jsonInputName: 'json-session-activity-expire-days',
                    default: 0
                },
            ],
            validateStep: 'validateStepSession',
            saveConfig: {
                onSave: (values) => {
                    console.log('Session Activity Expire Days:', values['i-session-activity-expire-days']);
                    console.log('Session Activity Expire Hours:', values['i-session-activity-expire-hours']);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-add-email',
            fields: [
                {
                    inputSettingName: 'i-mail-name',
                    principal: true,
                    errorFieldId: 'a-mail-name',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-mail-host',
                    errorFieldId: 'a-mail-host',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-mail-port',
                    errorFieldId: 'a-mail-port',
                    rules: {required: true, pattern: /^\d+$/}
                },
                {
                    inputSettingName: 'i-mail-user',
                    errorFieldId: 'a-mail-user',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-mail-password',
                    errorFieldId: 'a-mail-password',
                    rules: {
                        required: true,
                        pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.@*_]).{11,}$/,
                        comparison: 'identical',
                        input: 'i-mail-re-password'
                    },
                    password: true,
                },
                {
                    inputSettingName: 'i-mail-re-password',
                    errorFieldId: 'a-mail-re-password',
                    rules: {required: true, comparison: 'identical', input: 'i-mail-password'},
                    password: true,
                },
                {
                    inputSettingName: 'i-mail-default',
                    errorFieldId: 'a-mail-default',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-mail-prefix',
                    errorFieldId: 'a-mail-prefix',
                },
                {
                    inputSettingName: 'i-mail-protocol',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-mail-auth',
                    rules: {required: true},
                },
                {
                    inputSettingName: 'i-mail-debug',
                },
                {
                    inputSettingName: 'i-mail-test',
                }
            ],
            validateStep: 'validateEmailConfig',
            addConfig: {
                container: 'email-content',
                targetElement: 'json-mail-conf',
                label: 'Mail Configuration',
                editModalId: 'modal-add-email',
                onAdd: (values) => {
                    console.log('New email configuration added:', values);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-add-ftp',
            fields: [
                {
                    inputSettingName: 'i-ftp-name',
                    principal: true,
                    errorFieldId: 'a-ftp-name',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-ftp-host',
                    errorFieldId: 'a-ftp-host',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-ftp-port',
                    errorFieldId: 'a-ftp-port',
                    rules: {required: true, pattern: /^\d+$/}
                },
                {
                    inputSettingName: 'i-ftp-user',
                    errorFieldId: 'a-ftp-user',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-ftp-password',
                    errorFieldId: 'a-ftp-password',
                    rules: {
                        required: true,
                        pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.@*_]).{11,}$/,
                        comparison: 'identical',
                        input: 'i-ftp-re-password'
                    },
                    password: true,
                },
                {
                    inputSettingName: 'i-ftp-re-password',
                    errorFieldId: 'a-ftp-re-password',
                    rules: {required: true, comparison: 'identical', input: 'i-ftp-password'},
                    password: true,
                },
                {
                    inputSettingName: 'i-ftp-path',
                    errorFieldId: 'a-ftp-path',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-ftp-mode',
                    errorFieldId: 'i-ftp-mode',
                },
            ],
            validateStep: 'validateFTPConfig',
            addConfig: {
                container: 'ftp-content',
                targetElement: 'json-ftp-conf',
                label: 'FTP Configuration',
                editModalId: 'modal-add-ftp',
                onAdd: (values) => {
                    console.log('New ftp configuration added:', values);
                }
            }
        });

        await this.setupModalListener({
            modalId: 'modal-add-database',
            fields: [
                {
                    inputSettingName: 'i-database-name',
                    principal: true,
                    errorFieldId: 'a-database-name',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-database-host',
                    errorFieldId: 'a-database-host',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-database-port',
                    errorFieldId: 'a-database-port',
                    rules: {required: true, pattern: /^\d+$/}
                },
                {
                    inputSettingName: 'i-database-user',
                    errorFieldId: 'a-database-user',
                    rules: {required: true}
                },
                {
                    inputSettingName: 'i-database-password',
                    errorFieldId: 'a-database-password',
                    rules: {
                        required: true,
                        pattern: /^(?=.*[A-Z])(?=.*[a-z])(?=.*\d)(?=.*[.@*_]).{11,}$/,
                        comparison: 'identical',
                        input: 'i-database-re-password'
                    },
                    password: true,
                },
                {
                    inputSettingName: 'i-database-re-password',
                    errorFieldId: 'a-database-re-password',
                    rules: {required: true, comparison: 'identical', input: 'i-database-password'},
                    password: true,
                },
            ],
            validateStep: 'validateDatabaseConfig',
            addConfig: {
                container: 'database-content',
                targetElement: 'json-database-conf',
                label: 'Database Configuration',
                editModalId: 'modal-add-database',
                onAdd: (values) => {
                    console.log('New database configuration added:', values);
                }
            }
        });

        await this.setupSaveJsonListener();
    }

    async WelcomeInstaller() {

        await HandlerAudio.autoPlayAudio('welcome-lh');

    }


    async Installer() {
        await this.showWelcome();
        await this.setProtocol();
        await this.setDomain();
        await this.closeBtnAlertEvent();
        await this.handlerModal.initializeModals(this.modalids);
        await this.setupEventListeners();
        await this.errorViewer();
        await this.WelcomeInstaller();
    }


    async init() {
        if (!this.isSetup) return;

        try {
            await this.output.defaultMGS('loader', 'Installer Assets');
            await this.Installer();
            await this.output.defaultMGS('end-loader', 'Installer Assets');
        } catch (error) {
            await HandlerConsoleOutput.defaultMGS('error', 'Installer Interfaces', error);
        }
    }


    async errorViewer() {

        const iframe = document.getElementById('error-viewer');
        const reloadBtn = document.getElementById('reload-btn');
        const htmlFile = 'error.html';

        iframe.src = htmlFile;

        reloadBtn.addEventListener('click', () => {
            iframe.src = '';
            iframe.src = htmlFile;
        });
    }
}


(async () => {
        try {
            const handlerInstaller = new HandlerInstaller();
            await handlerInstaller.init();
        } catch (error) {
            console.error('Error initializing HandlerInstaller:', error);
        }
    }
)
();