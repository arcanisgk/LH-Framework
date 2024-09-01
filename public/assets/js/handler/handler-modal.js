export class HandlerModal {
    constructor() {
        this.modalInstances = {};
    }

    initializeModals(modalIds) {
        modalIds.forEach(id => {
            let modalElement = document.getElementById(id);
            if (modalElement) {
                this.modalInstances[id] = new bootstrap.Modal(modalElement);
            }
        });
    }

    hideModal(modalId) {
        if (this.modalInstances[modalId]) {
            this.modalInstances[modalId].hide();
        }
    }
}