import {HandlerConsoleOutput} from "./handler-console-output.js";

export class HandlerInstaller {

    constructor() {
        this.isSetup = (window.location.pathname === '/setup');
        this.output = new HandlerConsoleOutput();
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

    async Installer() {
        this.setProtocol();
        this.setDomain();
        this.closeBtnAlertEvent();
        console.log('FIN');
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