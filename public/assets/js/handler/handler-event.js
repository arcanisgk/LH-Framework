export class HandlerEvents {
    constructor(formSelector) {
        this.form = document.querySelector(formSelector);
        if (this.form) {
            this.initialize();
        }
    }

    initialize() {
        this.form.addEventListener('submit', (event) => {
            event.preventDefault();
            console.log("Formulario enviado");
            // Aquí manejas la lógica del formulario
        });
    }
}