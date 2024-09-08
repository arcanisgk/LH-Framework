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