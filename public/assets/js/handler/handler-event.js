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