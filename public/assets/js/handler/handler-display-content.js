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

/**
 * Displays content based on the provided configuration.
 *
 * @param {Array<{field: string, status: string, smg: string, in: string}>} contents - An array of content configurations, each with a field, status, message, and target container.
 * @returns {Promise<void>} - A Promise that resolves when the content has been displayed.
 */
export class HandlerDisplayContent {

    /**
     * @param {Array<{field: string, status: string, smg: string, in: string}>} contents
     */
    static async displayContent(contents) {
        console.log(contents);
        contents.forEach(content => {
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
                } else if (content.in === 'modal') {
                    console.log('modal');
                    //const modal = new bootstrap.Modal(container);
                    //modal.show();
                } else if (content.in !== 'modal' && content.in != null) {
                    console.log('Target/Plugin');

                } else {

                    HandlerDOM.showContent(container);
                    container.innerHTML = content.smg;

                }
            }
        });
    }
}