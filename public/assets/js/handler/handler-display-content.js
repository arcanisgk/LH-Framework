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
import {HandlerDOM} from "./handler-dom.js";
import {DatatablePlugin} from "./plugins/pl-datatable.js";

/**
 * Displays content based on the provided configuration.
 *
 * @param {Array<{field: string, status: string, smg: string, in: string}>} contents - An array of content configurations, each with a field, status, message, and target container.
 * @returns {Promise<void>} - A Promise that resolves when the content has been displayed.
 */
export class HandlerDisplayContent {


    /**
     * Displays content based on the provided configuration.
     *
     * @param {object} contents - The data object containing the HTML content and the selector to update.
     * @param {string} contents.field - The CSS selector of the element to update.
     * @param {string} contents.status - The status of the content: success, error, warning, info.
     * @param {string} contents.smg - The message to display.
     * @param {string} contents.in - The CSS selector of the element to update.
     * @param {string} contents.html - The HTML content to be inserted into the selected element.
     * @param {string} contents.outputFormat - Format of the output data.
     * @param {string} contents.typeTarget - Type of element to update: modal, table, select2.
     * @returns {Promise<void>} - A Promise that resolves when the content has been displayed.
     */
    static async displayContent(contents) {
        contents.forEach(content => {
            if (!content.field) return;
            const container = document.getElementById(content.field);
            console.log(container);
            if (container) {
                if (content.status === 'valid') {
                    console.log('valid');
                    const inputFieldId = content.field.replace('-smg', '');
                    const inputElement = document.getElementById(inputFieldId);

                    if (inputElement) {
                        inputElement.classList.add('is-valid');
                        container.className = 'valid-feedback';
                        container.innerHTML = content.smg
                            .replace('<div class="valid-feedback">', '')
                            .replace('</div>', '')
                            .trim();
                    }
                } else if (contents.in && content.in === 'modal') {

                    if (content.in === 'modal') {
                        console.log('modal');
                        //const modal = new bootstrap.Modal(container);
                        //modal.show();
                    } else if (content.in !== 'modal' && content.in != null) {
                        console.log('Target/Plugin');
                    }

                } else {

                    HandlerDOM.showContent(container);
                    container.innerHTML = content.smg;

                }
            }
        });
    }

    /**
     * Updates the plugin based on the provided data.
     * @param {object} data - The data object containing the HTML content and the selector to update.
     * @param {string} data.in - The CSS selector of the element to update.
     * @param {string} data.html - The HTML content to be inserted into the selected element.
     * @param {string} data.outputFormat - Format of the output data.
     * @param {object|string} data.content - The content to be inserted into the selected element.
     * @param {string} data.typeTarget - Type of element to update: modal, table, select2.
     */
    static async updatePlugin(data) {
        if (data.typeTarget) {
            switch (data.typeTarget) {
                case 'modal':
                    console.log('under development');
                    break;
                case 'table':
                    /* en este punto se debe implementar el manejador de tablas existente que actualizara el contenido de la tabla, el identificador de la tabla esta en la variable data.in */
                    console.log('under development table update');

                    await DatatablePlugin.updateTable({'target': data.in, 'content': JSON.parse(data.content[0])});

                    break;
                case 'select2':
                    console.log('under development');
                    break;

            }
        }
    }
}