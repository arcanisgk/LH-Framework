import {HandlerModal} from "../../handler/handler-modal.js";
import {HandlerUtilities} from "../../handler/handler-utilities.js";

// Initialize modal handler
const modalHandler = new HandlerModal();

// Example 1: Initialize existing modal from HTML
const existingModal = modalHandler.initializeModals('exampleModal');

// Example 2: Show/Hide existing modal
document.getElementById('showModalBtn').addEventListener('click', () => {
    existingModal.show();
});

document.getElementById('hideModalBtn').addEventListener('click', () => {
    modalHandler.hideModal('exampleModal');
});

// Example 3: Create and show a dynamic modal
document.getElementById('createModalBtn').addEventListener('click', async () => {
    const config = HandlerModal.initModal();
    config.title = 'Dynamic Modal';
    config.content = '<p>This is a dynamically created modal</p>';
    config.size = 'lg';
    config.fullscreen = 'sm-down';
    config.footerButtons = [
        {
            text: 'Save',
            class: 'btn-primary',
            onClick: () => console.log('Save clicked')
        }
    ];
    await HandlerModal.showModal(config);
});

// Example 4: Show wait modal
document.getElementById('waitModalBtn').addEventListener('click', async () => {
    const waitModal = await HandlerModal.showWaitModal();
    setTimeout(() => {
        waitModal.hide();
    }, 3000);
});

// Example 5: Show error modal
document.getElementById('errorModalBtn').addEventListener('click', () => {
    HandlerModal.showErrorModal('An error occurred!', false);
});

// Example 6: Show system error modal
document.getElementById('systemErrorBtn').addEventListener('click', () => {
    HandlerModal.showErrorModal('System malfunction detected', true);
});

// Example 7: Show success modal
document.getElementById('successModalBtn').addEventListener('click', () => {
    HandlerModal.showSuccessModal('Operation completed successfully!');
});

// Example 8: Modal with custom configuration
document.getElementById('customModalBtn').addEventListener('click', async () => {
    const config = HandlerModal.initModal();
    config.title = 'Custom Modal';
    config.content = `
            <form>
                <div class="mb-3">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" class="form-control">
                </div>
            </form>
        `;
    config.size = 'md';
    config.fullscreen = 'lg-down';
    config.backdrop = true;
    config.keyboard = true;
    config.footerButtons = [
        {
            text: 'Submit',
            class: 'btn-primary',
            onClick: () => console.log('Form submitted')
        },
        {
            text: 'Reset',
            class: 'btn-secondary',
            onClick: () => console.log('Form reset')
        }
    ];
    await HandlerModal.showModal(config);
});

document.getElementById('sequentialModalBtn').addEventListener('click', async () => {
    const sequence = {
        0: {
            title: 'Start',
            content: 'Initial step',
            size: 'sm'
        },
        1: {
            title: 'Choice',
            content: 'Choose your path',
            size: 'lg',
            fullscreen: 'sm-down',
            branches: [
                {
                    text: 'Path A',
                    class: 'btn-info',
                    nextStep: 2
                },
                {
                    text: 'Path B',
                    class: 'btn-warning',
                    nextStep: 3
                }
            ]
        },
        2: {
            title: 'Path A Selected',
            content: 'You chose path A',
            size: 'xl',
            nextStep: 4
        },
        3: {
            title: 'Path B Selected',
            content: 'You chose path B',
            fullscreen: true,
            nextStep: 4
        },
        4: {
            title: 'Final Step',
            content: 'Completion step',
            size: 'xxl'
        }
    };

    await HandlerModal.showSequentialModals(sequence, {
        enableNavigation: true,
        onComplete: () => console.log('Sequence completed with branching'),
        size: 'md',
        fullscreen: false,
        nextText: 'Continue',
        backText: 'Previous'
    });
});


const config = HandlerModal.initModal();
config.title = 'Landscape Mode Required';
config.content = 'Please rotate your device to Landscape mode to continue.';
config.showTitleClose = false;
config.showFooterClose = false;
config.backdrop = 'static';
config.keyboard = false;
config.type = 'warning';
config.show = false;

const modalInstance = HandlerModal.createModal(config);

HandlerUtilities.aplicarOnChangeDeviceOrientation({
    onLandscape: () => {
        if (modalInstance._isShown) {
            modalInstance.hide();
        }
    },
    onPortrait: () => {
        if (!modalInstance._isShown) {
            modalInstance.show();
        }
    }
});
