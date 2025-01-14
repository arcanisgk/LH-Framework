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

export class HandlerModal {


    /**
     * Initializes the HandlerModal class by setting up internal data structures to manage modals.
     * The constructor initializes a Map to store modal instances, an array to track active modals,
     * and a counter to keep track of the number of modals.
     */
    constructor() {
        this.modals = new Map();
        this.activeModals = [];
        this.modalCount = 0;
    }

    /**
     * Initializes a configuration object for a modal.
     * The configuration object contains various properties that can be used to customize the modal's appearance and behavior.
     * @returns {Object} - The initialized modal configuration object.
     */
    static initModal() {
        return {
            zIndex: 2050,
            size: 'md',
            type: '',
            target: '',
            title: '',
            content: '',
            showTitleClose: true,
            showFooterClose: true,
            footerButtons: [],
            refresh: false,
            backdrop: 'static',
            keyboard: false,
            focus: true,
            show: true,
            sound: false,
            hideAll: true,
            autoHide: false,
            removeErrors: false,
            navigate: false,
            reloadPlugins: true,
            forceHtmlConversion: true
        };
    }

    /**
     * Hides all visible modals on the page, except for those with the `data-form="true"` attribute.
     * This method waits for each modal to be fully hidden before resolving the returned Promise.
     * @returns {Promise<boolean>} - A Promise that resolves to `true` when all visible modals have been hidden.
     */
    static async hideAllModals() {
        const visibleModals = document.querySelectorAll('.modal.show:not([data-form="true"])');
        if (visibleModals.length === 0) return true;

        const hidePromises = Array.from(visibleModals).map(modal => {
            return new Promise(resolve => {
                const bsModal = bootstrap.Modal.getInstance(modal);
                modal.addEventListener('hidden.bs.modal', () => resolve(), {once: true});
                bsModal.hide();
            });
        });

        await Promise.all(hidePromises);
        return true;
    }

    /**
     * Generates a unique modal ID by appending a numeric suffix to the provided prefix.
     * If the generated ID already exists in the DOM, it will continue incrementing the suffix until a unique ID is found.
     * @param {string} prefix - The prefix to use for the modal ID.
     * @param {number} id - The initial ID value to use.
     * @returns {string} - The generated unique modal ID.
     */
    static generateModalId(prefix, id) {
        let modalId = `${prefix}${id}`;
        while (document.getElementById(modalId)) {
            id += 10;
            modalId = `${prefix}${id}`;
        }
        return modalId;
    }

    /**
     * Creates a new modal element and initializes it with the provided configuration.
     * @param {Object} config - The configuration object for the modal.
     * @param {number} [config.zIndex=2050] - The z-index of the modal.
     * @param {string} [config.size='md'] - The size of the modal ('sm', 'md', 'lg', 'xl').
     * @param {string} [config.type=''] - The type of the modal, used to determine the header and button classes.
     * @param {string} [config.target=''] - The target element for the modal, used to generate a unique ID.
     * @param {string} [config.title=''] - The title of the modal.
     * @param {string} [config.content=''] - The content of the modal body.
     * @param {boolean} [config.showTitleClose=true] - Whether to show the close button in the modal header.
     * @param {boolean} [config.showFooterClose=true] - Whether to show the close button in the modal footer.
     * @param {Array<{text: string, class?: string, onClick?: Function}>} [config.footerButtons=[]] - An array of footer buttons to display.
     * @param {boolean} [config.refresh=false] - Whether to refresh the page when the modal is closed.
     * @param {string} [config.backdrop='static'] - The backdrop setting for the modal ('static', true, false).
     * @param {boolean} [config.keyboard=false] - Whether the modal should be closable by the keyboard.
     * @param {boolean} [config.focus=true] - Whether the modal should focus on the first focusable element.
     * @param {boolean} [config.show=true] - Whether to show the modal immediately after creation.
     * @param {HTMLElement} [config.sourceButton] - The source button element for the modal.
     * @param {boolean} [config.sound=false] - Whether to play a sound when the modal is shown.
     * @param {boolean} [config.hideAll=true] - Whether to hide all existing modals before displaying the new one.
     * @param {boolean} [config.autoHide=false] - Whether to automatically hide the modal after a certain time.
     * @param {boolean} [config.removeErrors=false] - Whether to remove any existing error messages when the modal is shown.
     * @param {boolean} [config.navigate=false] - Whether to navigate to a specific page when the modal is closed.
     * @param {boolean} [config.reloadPlugins=true] - Whether to reload any plugins when the modal is shown.
     * @param {boolean} [config.forceHtmlConversion=true] - Whether to force HTML conversion for the modal content.
     * @returns {bootstrap.Modal} - The created modal instance.
     */
    static createModal(config) {
        let modalId;

        if (config.sourceButton && config.sourceButton instanceof HTMLElement) {
            const existingModalId = config.sourceButton.getAttribute('data-modal-id');
            if (existingModalId) {
                modalId = existingModalId;
            } else {
                modalId = config.target || this.generateModalId('modal-', Date.now());
                // Store modal ID in button
                config.sourceButton.setAttribute('data-modal-id', modalId);
            }
        } else {
            modalId = config.target || this.generateModalId('modal-', Date.now());
        }

        let existingModal = document.getElementById(modalId);
        if (existingModal) {
            const bsModal = bootstrap.Modal.getInstance(existingModal) || new bootstrap.Modal(existingModal, {
                backdrop: config.backdrop,
                keyboard: config.keyboard,
                focus: config.focus
            });

            if (config.show) bsModal.show();
            return bsModal;
        }

        const modal = document.createElement('div');
        modal.id = modalId;
        modal.className = 'modal fade';
        modal.setAttribute('tabindex', '-1');
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-labelledby', `${modalId}-title`);
        modal.style.zIndex = config.zIndex;

        const modalDialog = document.createElement('div');
        let dialogClasses = [];

        if (config.dialogClass) {
            dialogClasses.push(`modal-dialog ${config.dialogClass} modal-dialog-scrollable`);
        } else {
            dialogClasses.push(`modal-dialog modal-${config.size} modal-dialog-centered modal-dialog-scrollable`);
        }

        modalDialog.className = dialogClasses.join(' ');
        modalDialog.setAttribute('role', 'document');

        const modalContent = document.createElement('div');
        modalContent.className = 'modal-content';

        // Header with type-specific styling
        const header = document.createElement('div');
        header.className = `modal-header ${this.getModalHeaderClass(config.type)}`;

        const title = document.createElement('h1');
        title.className = 'modal-title fs-4';
        title.id = `${modalId}-title`;
        title.textContent = config.title;

        const closeBtn = document.createElement('button');
        closeBtn.type = 'button';
        closeBtn.className = 'btn-close';
        closeBtn.setAttribute('data-bs-dismiss', 'modal');
        closeBtn.setAttribute('aria-label', 'Close');

        header.append(title);
        if (config.showTitleClose) header.append(closeBtn);

        const body = document.createElement('div');
        body.className = 'modal-body';
        body.innerHTML = config.content;

        modalContent.append(header, body);

        if (config.footerButtons?.length || config.showFooterClose) {
            const footer = document.createElement('div');
            footer.className = 'modal-footer';

            if (config.footerButtons?.length) {
                config.footerButtons.forEach(btn => {
                    const button = document.createElement('button');
                    button.type = 'button';
                    button.className = `btn ${btn.class || this.getModalButtonClass(config.type)}`;
                    button.textContent = btn.text;
                    if (btn.onClick) {
                        button.addEventListener('click', (e) => {
                            e.preventDefault();
                            btn.onClick();
                        });
                    }
                    footer.append(button);
                });
            }

            if (config.showFooterClose) {
                const footerCloseBtn = document.createElement('button');
                footerCloseBtn.type = 'button';
                footerCloseBtn.className = `btn ${this.getModalButtonClass(config.type)}`;
                footerCloseBtn.setAttribute('data-bs-dismiss', 'modal');
                footerCloseBtn.textContent = 'Close';
                footer.append(footerCloseBtn);
            }

            modalContent.append(footer);
        }

        modalDialog.append(modalContent);
        modal.append(modalDialog);

        modal.addEventListener('shown.bs.modal', () => {
            const firstFocusableElement = modal.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
            if (firstFocusableElement) {
                firstFocusableElement.focus();
            }
        });

        modal.addEventListener('hide.bs.modal', () => {
            const focusedElement = document.activeElement;
            if (focusedElement && modal.contains(focusedElement)) {
                focusedElement.blur();
            }
        });

        modal.addEventListener('hidden.bs.modal', () => {
            if (config.refresh) window.location.reload();
            if (config.navigate) this.handleNavigation();
            //modal.remove();
            modal.style.display = 'none';
        });

        document.body.append(modal);

        const bsModal = new bootstrap.Modal(modal, {
            backdrop: config.backdrop,
            keyboard: config.keyboard,
            focus: config.focus
        });

        if (config.show) bsModal.show();

        return bsModal;
    }

    static destroyModal(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            const bsModal = bootstrap.Modal.getInstance(modal);
            if (bsModal) {
                bsModal.dispose();
            }
            modal.remove();
        }
    }

    /**
     * Gets the CSS class for a modal header based on the provided type.
     * @param {string} type - The type of the modal, used to determine the header class.
     * @returns {string} The CSS class for the modal header.
     */
    static getModalHeaderClass(type) {
        const classes = {
            'error': 'bg-danger text-white',
            'user-error': 'bg-warning text-white',
            'system-error': 'bg-danger text-white',
            'warning': 'bg-warning text-white',
            'success': 'bg-success text-white',
            'info': 'bg-info text-white',
            'custom': 'bg-primary text-white',
            'sequential': 'bg-secondary text-white'
        };
        return classes[type] || 'bg-light';
    }

    /**
     * Gets the CSS class for a modal button based on the provided type.
     * @param {string} type - The type of the modal, used to determine the button class.
     * @returns {string} The CSS class for the modal button.
     */
    static getModalButtonClass(type) {
        const classes = {
            'error': 'btn-danger',
            'user-error': 'btn-warning',
            'system-error': 'btn-danger',
            'warning': 'btn-warning',
            'success': 'btn-success',
            'info': 'btn-info',
            'custom': 'btn-primary',
            'sequential': 'btn-secondary'
        };
        return classes[type] || 'btn-primary';
    }

    /**
     * Displays a modal with the provided configuration.
     * @param {Object} config - The configuration object for the modal.
     * @param {boolean} [config.hideAll=false] - Whether to hide all existing modals before displaying the new one.
     * @returns {Promise<bootstrap.Modal>} - A Promise that resolves to the created modal instance.
     */
    static async showModal(config) {
        if (config.hideAll) {
            await this.hideAllModals();
        }

        return this.createModal(config);
    }

    /**
     * Displays a modal with a loading spinner to indicate that the application is waiting for a response.
     * @returns {Promise<bootstrap.Modal>} - A Promise that resolves to the created modal instance.
     */
    static showWaitModal() {
        const config = this.initModal();
        config.title = 'Please Wait...';
        config.content = '<div class="text-center"><div class="spinner-border" role="status"></div></div>';
        config.showFooterClose = false;
        return this.showModal(config);
    }

    /**
     * Displays an error modal with the provided message.
     * @param {string} message - The error message to display in the modal.
     * @param {boolean} [isSystem=false] - Whether the error is a system error or a user error. This determines the title and styling of the modal.
     * @returns {Promise<bootstrap.Modal>} - A Promise that resolves to the created modal instance.
     */
    static showErrorModal(message, isSystem = false) {
        const config = this.initModal();
        config.size = 'lg';
        config.title = isSystem ? 'System Error' : 'User Error';
        config.content = message;
        config.type = isSystem ? 'system-error' : 'user-error';
        return this.showModal(config);
    }

    /**
     * Displays a success modal with the provided message.
     * @param {string} message - The message to display in the success modal.
     * @returns {Promise<bootstrap.Modal>} - A Promise that resolves to the created modal instance.
     */
    static showSuccessModal(message) {
        const config = this.initModal();
        config.title = 'Success';
        config.content = message;
        config.type = 'success';
        return this.showModal(config);
    }

    /**
     * Displays a sequence of modals with navigation and branching options.
     * @param {Object} sequence - An object containing the configuration for each step in the sequence.
     * @param {Object} [options] - Optional configuration options for the sequence.
     * @param {boolean} [options.enableNavigation=true] - Whether to enable navigation buttons (Back, Next) in the modals.
     * @param {function} [options.onComplete] - A callback function to be executed when the sequence is completed.
     * @param {string} [options.type='sequential'] - The type of the modals, used for styling purposes.
     * @param {string} [options.backText='Back'] - The text for the back button.
     * @param {string} [options.nextText='Next'] - The text for the next button.
     * @param {string} [options.finishText='Finish'] - The text for the finish button.
     * @param {string} [options.size='md'] - The size of the modals (sm, md, lg, xl, xxl).
     * @param {boolean|string} [options.fullscreen=false] - Whether the modals should be fullscreen (true, false, 'sm-down', 'md-down', 'lg-down', 'xl-down', 'xxl-down').
     * @returns {Promise<bootstrap.Modal>} - A Promise that resolves to the last modal instance in the sequence.
     */
    static async showSequentialModals(sequence, options = {}) {
        const defaultOptions = {
            enableNavigation: true,
            onComplete: () => console.log('Sequence completed'),
            type: 'sequential',
            backText: 'Back',
            nextText: 'Next',
            finishText: 'Finish',
            size: 'md', // sm, md, lg, xl, xxl
            fullscreen: false, // true, false, 'sm-down', 'md-down', 'lg-down', 'xl-down', 'xxl-down'
        };

        const config = {...defaultOptions, ...options};
        const history = [];
        const sequenceKeys = Object.keys(sequence);
        const lastStepIndex = sequenceKeys[sequenceKeys.length - 1];

        const showStep = async (step) => {
            const currentStep = sequence[step];
            const modalConfig = HandlerModal.initModal();
            modalConfig.title = currentStep.title;
            modalConfig.content = currentStep.content;
            modalConfig.type = config.type;
            modalConfig.size = currentStep.size || config.size;
            modalConfig.footerButtons = [];

            // Set modal size and fullscreen options
            if (currentStep.fullscreen || config.fullscreen) {
                const fullscreenValue = currentStep.fullscreen || config.fullscreen;
                modalConfig.dialogClass = fullscreenValue === true ?
                    'modal-fullscreen' :
                    `modal-fullscreen-${fullscreenValue}`;
            }

            // Navigation buttons - only show if enabled in config
            if (config.enableNavigation && history.length > 0) {
                modalConfig.footerButtons.push({
                    text: config.backText,
                    class: 'btn-secondary',
                    onClick: async () => {
                        const modal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
                        modal.hide();
                        const previousStep = history.pop();
                        await showStep(previousStep);
                    }
                });
            }

            // Handle branching paths
            if (currentStep.branches) {
                currentStep.branches.forEach(branch => {
                    modalConfig.footerButtons.push({
                        text: branch.text,
                        class: branch.class || 'btn-primary',
                        onClick: async () => {
                            const modal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
                            modal.hide();
                            history.push(step);
                            await showStep(branch.nextStep);
                        }
                    });
                });
            }
            // Show finish button only on last step
            else if (step === parseInt(lastStepIndex)) {
                modalConfig.footerButtons.push({
                    text: config.finishText,
                    class: 'btn-success',
                    onClick: config.onComplete
                });
            }
            // Regular next step
            else {
                const nextStepIndex = currentStep.nextStep || parseInt(step) + 1;
                modalConfig.footerButtons.push({
                    text: config.nextText,
                    class: 'btn-primary',
                    onClick: async () => {
                        const modal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
                        modal.hide();
                        history.push(step);
                        await showStep(nextStepIndex);
                    }
                });
            }

            return await HandlerModal.showModal(modalConfig);
        };

        return await showStep(sequenceKeys[0]);
    }


    /**
     * Initializes a modal with the specified ID.
     * @param {string} modalId - The ID of the modal to initialize.
     * @returns {bootstrap.Modal} - The initialized Bootstrap modal instance.
     */
    initializeModals(modalId) {
        const modalElement = document.getElementById(modalId);
        if (!modalElement) return;

        const bsModal = new bootstrap.Modal(modalElement, {
            backdrop: 'static',
            keyboard: false
        });

        this.modals.set(modalId, {
            element: modalElement,
            instance: bsModal
        });

        modalElement.addEventListener('shown.bs.modal', () => {
            this.activeModals.push(modalId);
            this.modalCount++;
        });

        modalElement.addEventListener('hidden.bs.modal', () => {
            const index = this.activeModals.indexOf(modalId);
            if (index > -1) {
                this.activeModals.splice(index, 1);
            }
            this.modalCount--;
        });

        return bsModal;
    }

    /**
     * Hides the modal with the specified ID.
     * @param {string} modalId - The ID of the modal to hide.
     * @returns {boolean} - True if the modal was successfully hidden, false otherwise.
     */
    hideModal(modalId) {
        const modal = this.modals.get(modalId);
        if (modal?.instance) {
            modal.instance.hide();
            return true;
        }
        return false;
    }
}