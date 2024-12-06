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


export class HandlerSwRegister {
    static async register() {
        if ('serviceWorker' in navigator) {
            try {
                const registration = await navigator.serviceWorker.register('/assets/js/handler/handler-sw.js');
                //console.log('Service Worker registered successfully', registration);
                return registration;
            } catch (error) {
                //console.error('Service Worker registration failed:', error);
                throw error;
            }
        } else {
            //console.warn('Service Workers are not supported in this browser');
            return null;
        }
    }

    static async unregister() {
        if ('serviceWorker' in navigator) {
            const registration = await navigator.serviceWorker.ready;
            const result = await registration.unregister();
            /*
            if (result) {
                console.log('Service Worker unregistered successfully');
            } else {
                console.warn('Service Worker unregister failed');
            }*/
            return result;
        }
        return false;
    }

    static getRegistration() {
        return navigator.serviceWorker.getRegistration();
    }
}