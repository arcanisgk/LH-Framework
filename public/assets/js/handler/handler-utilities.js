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

/**
 * Utility class for handling various functionality related to the application.
 */
export class HandlerUtilities {
    /**
     * @param {string} text - The text to wrap.
     * @param {number} width - The desired total width for the wrapped text.
     * @returns {string} - The wrapped text with right dashes.
     */
    static padRightWithDashes(text, width) {
        if (text.length >= width) {
            return text;
        }
        const paddingLength = width - text.length;
        const padding = '-'.repeat(paddingLength);
        return text + padding;
    }

    /**
     * Converts a data URI to a binary array.
     *
     * @param {string} dataURI - The data URI to convert.
     * @returns {Uint8Array} - The binary array representation of the data URI.
     */
    static convertDataURIToBinary(dataURI) {
        const BASE64_MARKER = ';base64,';
        const base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        const base64 = dataURI.substring(base64Index);
        const raw = window.atob(base64);
        const rawLength = raw.length;
        const array = new Uint8Array(rawLength);

        for (let i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }

        return array;
    }

    /**
     * Finds the closest parent element that is a modal body.
     *
     * @param {HTMLElement} target - The target element to start the search from.
     * @returns {Object} - An object with the target element and the length of the search.
     */
    static findModalParent(target) {

        const panelBody = target.closest('.panel-body');
        if (panelBody && panelBody.length > 0) {
            return {target: panelBody, length: 1};
        }

        const modalBody = target.closest('.modal-body');
        if (modalBody && modalBody.length > 0) {
            return {target: target.parents('.modal-body'), length: 1};
        }

        return {length: 0};
    }

    /**
     * Checks if the current device orientation is in portrait mode.
     *
     * @returns {boolean} - `true` if the device orientation is in portrait mode, `false` otherwise.
     */
    static isPortrait() {
        return window.matchMedia("(orientation: portrait)").matches;
    }

    /**
     * Checks if the current device orientation is in landscape mode.
     *
     * @returns {boolean} - `true` if the device orientation is in landscape mode, `false` otherwise.
     */
    static isLandscape() {
        return window.matchMedia("(orientation: landscape)").matches;
    }

    /**
     * Applies a function to be called when the device orientation changes.
     *
     * @param {Object} options - An object with optional callback functions for portrait and landscape orientations.
     * @param {function} [options.onPortrait] - A callback function to be called when the device is in portrait orientation.
     * @param {function} [options.onLandscape] - A callback function to be called when the device is in landscape orientation.
     * @returns {function} - A function that can be called to remove the event listener for device orientation changes.
     */
    static aplicarOnChangeDeviceOrientation(options = {}) {
        const {
            onPortrait = null,
            onLandscape = null
        } = options;

        const mediaQueryPortrait = window.matchMedia("(orientation: portrait)");

        const checkOrientation = (e) => {
            if (e.matches) {
                if (onPortrait && typeof onPortrait === 'function') {
                    onPortrait();
                }
            } else {
                if (onLandscape && typeof onLandscape === 'function') {
                    onLandscape();
                }
            }
        };

        setTimeout(() => {
            checkOrientation(mediaQueryPortrait);
        }, 100);

        mediaQueryPortrait.addEventListener('change', checkOrientation);

        return () => {
            mediaQueryPortrait.removeEventListener('change', checkOrientation);
        };
    }


    /**
     * Checks if an object is iterable.
     *
     * @param {*} obj - The object to check for iterability.
     * @returns {boolean} - True if the object is iterable, false otherwise.
     */
    static isIterable(obj) {
        if (obj == null) return false;
        return typeof obj[Symbol.iterator] === 'function' || Array.isArray(obj) || typeof obj === 'object';
    }
}

