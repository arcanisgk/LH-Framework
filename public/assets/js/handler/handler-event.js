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
import {HandlerResponse} from "./handler-response.js";

/**
 * Handles form events and processes them using the HandlerRequest and HandlerResponse classes.
 * The HandlerEvents class manages form event registration, form validation, and form data submission.
 */
export class HandlerEvents {

    /**
     * Initializes the HandlerEvents instance by setting up the events and forms properties.
     * The events property is an array to store registered events, and the forms property
     * is a collection of all form elements on the page.
     */
    constructor() {
        this.events = [];
        this.forms = document.querySelectorAll('form');
    }

    /**
     * Processes a form event by creating a new HandlerEvents instance and calling its processEvent method.
     *
     * @param {Event} event - The form event to be processed.
     * @param {HTMLFormElement} form - The form element associated with the event.
     * @returns {Promise<void>} - A Promise that resolves when the event processing is complete.
     */
    static async processEvent(event, form) {
        const eventHandler = new HandlerEvents();
        return await eventHandler.processEvent(event, form);
    }

    /**
     * Processes a form event by building the form data, validating the form, and then sending the data to the server using the HandlerRequest and HandlerResponse classes.
     *
     * @param {Event} event - The form event to be processed.
     * @param {HTMLFormElement} form - The form element associated with the event.
     * @returns {Promise<void>} - A Promise that resolves when the event processing is complete.
     */
    async processEvent(event, form) {

        const formData = this.buildFormData(form, event);
        if (!this.validateForm(form)) return;

        try {
            const responseData = await HandlerRequest.request({
                uri: form.action,
                data: formData,
                type: 'multipart/form-data',
                method: 'post'
            });
            if (responseData) {
                await HandlerResponse.processResponse(responseData);
            }
        } catch (error) {
            console.error('Event processing error:', error);
        }
    }

    /**
     * Registers an event in the `events` array of the `HandlerEvents` class.
     *
     * @param {Event} event - The event to be registered.
     */
    registerEvent(event) {
        this.events.push(event);
    }

    /**
     * Initializes the event listeners for all forms on the page.
     * This method iterates through all the forms on the page and calls the `setupFormEvents` method
     * to set up the necessary event listeners for each form.
     */
    async initializeEvents() {
        if (this.forms.length > 0) {
            this.forms.forEach(form => {
                this.setupFormEvents(form);
            });
        }
    }

    /**
     * Sets up event listeners for the form elements, including the submit event and event-related elements like buttons, selects, and checkboxes.
     *
     * @param {HTMLFormElement} form - The form element to set up the event listeners for.
     */
    setupFormEvents(form) {
        form.addEventListener('submit', (event) => {
            event.preventDefault();
        });

        const handleEvent = (event, form) => {
            this.processEvent(event, form);
        };

        ['button[type="button"][name="event"]',
            'select[name="event"]',
            'input[type="checkbox"][name="event"]'].forEach(selector => {
            const elements = form.querySelectorAll(selector);
            elements.forEach(element => {
                const eventType = element.tagName.toLowerCase() === 'button' ? 'click' : 'change';
                element.addEventListener(eventType, (event) => handleEvent(event, form));
            });
        });
    }

    /**
     * Builds a FormData object from the form and the clicked event target.
     *
     * @param {HTMLFormElement} form - The form element.
     * @param {Event} event - The event object.
     * @returns {FormData} - The constructed FormData object.
     */
    buildFormData(form, event) {
        const formData = new FormData();
        const buttonClicked = event.submitter || event.target;

        formData.append('uri_current', window.location.pathname);
        formData.append('uri_params', JSON.stringify(Object.fromEntries(new URLSearchParams(window.location.search))));

        if (buttonClicked.tagName.toLowerCase() === 'select' && buttonClicked.hasAttribute('data-lh-event')) {
            formData.append('event', buttonClicked.getAttribute('data-lh-event'));
        } else {
            formData.append('event', buttonClicked.value);
        }

        form.querySelectorAll('input, select, textarea').forEach(input => {

            let name = input.name === 'event' ? input.getAttribute('data-lh-var') : input.name;
            formData.append(name, input.type === 'checkbox' ? input.checked : input.value);

        });

        return formData;
    }

    /**
     * Validates the form by checking if all required fields have a value.
     *
     * @param {HTMLFormElement} form - The form element to validate.
     * @returns {boolean} - True if the form is valid, false otherwise.
     */
    validateForm(form) {
        let valid = true;
        form.querySelectorAll('input, select, textarea').forEach(input => {
            input.classList.remove('is-invalid');
            if (input.hasAttribute('required') && !input.value) {
                valid = false;
                input.classList.add('is-invalid');
                input.focus();
            }
        });
        return valid;
    }
}