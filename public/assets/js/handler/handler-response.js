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
import {HandlerError} from "./handler-error.js";
import {HandlerDisplayContent} from "./handler-display-content.js";

export class HandlerResponse {

    /**
     * Processes the response data and handles different scenarios based on the response content.
     * @param {object} responseData - The response data object.
     * @param {string} [responseData.nav] - The URL to redirect the user to.
     * @param {string} [responseData.html] - The HTML content to be inserted into the selected element.
     * @param {string} [responseData.in] - The CSS selector of the element to update.
     * @param {boolean} [responseData.isError] - Indicates whether the response is an error.
     * @param {boolean} [responseData.refresh] - Indicates whether the page should be refreshed.
     * @param {string} [responseData.outputFormat] - The format of the output data.
     * @returns {Promise<object>} - The processed response data.
     */
    static async processResponse(responseData) {
        try {
            //console.log(responseData);

            if (responseData.nav) {
                this.handleRedirect(responseData.nav);
                return;
            }

            if (responseData.in && (responseData.html || responseData.outputFormat)) {
                await this.updateDOM(responseData);
            }

            if (responseData.isError) {
                await this.handleError(responseData);
            } else {
                await this.handleSuccess(responseData);
            }

            if (responseData.refresh) {
                this.handleRefresh();
            }

            return responseData;
        } catch (error) {
            console.error('Response processing error:', error);
        }
    }

    /**
     * Redirects the user to the specified URL.
     * @param {string} url - The URL to redirect the user to.
     */
    static handleRedirect(url) {
        window.location.href = url;
    }

    /**
     * Updates the DOM with the provided HTML content.
     * @param {object} data - The data object containing the HTML content and the selector to update.
     * @param {string} data.in - The CSS selector of the element to update.
     * @param {string} data.html - The HTML content to be inserted into the selected element.
     * @param {string} data.outputFormat - Format of the output data.
     * @param {string} data.typeTarget - Type of element to update: modal, table, select2.
     */
    static async updateDOM(data) {

        if (data.outputFormat && data.outputFormat === 'json') {

            await HandlerDisplayContent.updatePlugin(data);
            return;
        }

        let target_element = document.querySelector(data.in);
        if (target_element) {
            target_element.innerHTML = data.html;
        }
    }

    /**
     * Handles the error case of a response.
     * Logs the content of the response and displays the error using the HandlerError.displayError method.
     * @param {object} data - The response data object.
     * @param {string} data.content - The error content to be displayed.
     * @returns {Promise<void>}
     */
    static async handleError(data) {
        console.warn(data.content);
        await HandlerError.displayError(data.content);
    }

    /**
     * Handles the success case of a response.
     * Logs the content of the response and displays it using the HandlerDisplayContent.displayContent method.
     * @param {object} data - The response data object.
     * @param {string} data.content - The content to be displayed.
     * @returns {Promise<void>}
     */
    static async handleSuccess(data) {
        try {
            if (!data.content) return;
            await HandlerDisplayContent.displayContent(data.content);
        } catch (error) {
            console.error('Display processing error:', error);
        }
    }

    /**
     * Refreshes the page after a 10-second delay.
     * This method is used to automatically reload the page after a certain time period,
     * which can be useful for scenarios where the page needs to be periodically updated
     * with new data.
     */
    static handleRefresh() {
        setTimeout(() => {
            window.location.reload();
        }, 10000);
    }
}
