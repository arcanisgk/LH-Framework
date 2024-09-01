export class HandlerDOM {
    static getInputElement(container, inputName) {
        return container.querySelector(`input[name='${inputName}']:not([type='hidden']`) ||
            container.querySelector(`select[name='${inputName}']`);
    }

    static getInputValue(inputElement) {
        return inputElement.type === 'checkbox' ? inputElement.checked : inputElement.value;
    }

    static createNewTextArea(targetSelector, initialValue) {
        const container = document.querySelector(targetSelector);
        if (!container) return;

        const textarea = document.createElement('textarea');
        textarea.value = initialValue;
        textarea.classList.add('form-control');
        container.appendChild(textarea);
    }

    static iconChange(element) {
        let iconElement = element.parentElement.querySelector("i");
        if (iconElement) {
            iconElement.classList.remove("text-body", "text-opacity-25", "text-danger");
            iconElement.classList.add("text-success");
        }
    }

    static showError(errorField) {
        errorField.classList.remove("hide");
        errorField.classList.add("show");
    }

    static hideError(errorField) {
        errorField.classList.remove("show");
        errorField.classList.add("hide");
    }
}