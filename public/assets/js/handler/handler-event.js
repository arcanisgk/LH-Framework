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
import {HandlerRequest} from "./handler-request.js";

export class HandlerEvents {

    constructor() {
        this.events = [];
        this.forms = document.querySelectorAll('form');
    }

    registerEvent(event) {
        this.events.push(event);
    }

    async initializeEvents() {
        if (this.forms.length > 0) {
            this.forms.forEach((form, index) => {
                form.addEventListener('submit', (event) => {
                    event.preventDefault();
                });

                const handleEvent = (event, form) => {
                    //const elementValue = event.target.type === 'checkbox' ? event.target.checked : event.target.value;
                    this.processEvent(event, form);
                };

                const buttons = form.querySelectorAll('button[type="button"][name="event"]');
                const selects = form.querySelectorAll('select[name="event"]');
                const checkboxes = form.querySelectorAll('input[type="checkbox"][name="event"]');

                if (buttons.length > 0) {
                    buttons.forEach(button => button.onclick = (event) => handleEvent(event, form));
                }

                if (selects.length > 0) {
                    selects.forEach(select => select.onchange = (event) => handleEvent(event, form));
                }

                if (checkboxes.length > 0) {
                    checkboxes.forEach(checkbox => checkbox.onchange = (event) => handleEvent(event, form));
                }
            })
        }
    }


    async processEvent(event, form) {
        const action = form.action;
        const inputElements = form.querySelectorAll('input, select, textarea');
        const buttonClicked = event.submitter || event.target;
        const eventValue = buttonClicked.value;
        const formData = new FormData();
        let valid = true;
        formData.append('uri_current', window.location.pathname);
        formData.append('uri_params', JSON.stringify(Object.fromEntries(new URLSearchParams(window.location.search))) || '');
        formData.append('event', eventValue);

        inputElements.forEach(input => {
            let fieldName = input.name;
            let value = input.type === 'checkbox' ? input.checked : input.value;
            input.classList.remove('is-invalid');
            if (input.hasAttribute('required') && !value) {
                valid = false;
                input.classList.add('is-invalid');
                input.focus();
                return;
            }
            formData.append(fieldName, value);
        });

        if (valid) {
            await HandlerRequest.request({
                uri: action,
                data: formData,
                type: 'multipart/form-data',
                method: 'post',
                response: 'json',
                error: 'json'
            });
        }
    }
}