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

import {HandlerAudio} from "./handler-audio.js";

/**
 * The `HandlerGlobalSetting` class provides methods for managing the global settings of the application.
 * It initializes the global settings, including settings related to form handling, error handling, voice AI,
 * server checks, scan keys, reloading, file uploads, search functionality, modified fields, sound settings,
 * and the list of available audio files.
 * The class also provides methods for getting, setting, and removing individual settings, as well as
 * retrieving the entire global settings object and resetting the settings to their initial state.
 */
export class HandlerGlobalSetting {

    static settings = {};

    /**
     * Initializes the global settings for the application.
     * This method is marked as `async` to allow for any asynchronous initialization
     * that may be required as part of the reset process.
     * It creates a new `HandlerAudio` instance, initializes it, and then
     * retrieves all the available audio files. The global settings object is then
     * populated with various configuration options, including settings related to
     * form handling, error handling, voice AI, server checks, scan keys, reloading,
     * file uploads, search functionality, modified fields, sound settings, and the
     * list of available audio files.
     */
    static async init() {

        const handlerAudio = new HandlerAudio();
        await handlerAudio.initialize();

        let sounds = handlerAudio.getAllAudio();

        this.settings = {
            preventOutForm: true,
            error: false,
            voiceIA: {},
            chkServer: true,
            scanKey: {
                opt1: 117,
                opt2: 0,
            },
            reload: true,
            upload: {
                size: 2,
                max: 5,
                types: ['jpeg', 'jpg', 'pdf', 'xlsx', 'xlsx'],
                extension: ['jpeg', 'jpg', 'pdf', 'xlsx', 'xlsx'],
            },
            search: {
                timer: 5000,
                interval: 3500,
            },
            modifiedField: [],
            sound: true,
            soundList: sounds
        };

    }

    /**
     * Gets the value of a setting from the global settings object.
     * @param {string} key - The key of the setting to retrieve.
     * @returns {*} The value of the setting, or null if the setting does not exist.
     */
    static getSetting(key) {
        return this.settings[key] ?? null;
    }

    /**
     * Sets a value in the global settings object.
     * @param {string} key - The key of the setting to set.
     * @param {*} value - The value to set for the given key.
     */
    static setSetting(key, value) {
        this.settings[key] = value;
    }

    /**
     * Returns the global settings object.
     * @returns {Object} The global settings object.
     */
    static getAllSettings() {
        return this.settings;
    }

    /**
     * Removes a setting from the global settings object.
     * @param {string} key - The key of the setting to remove.
     */
    static removeSetting(key) {
        delete this.settings[key];
    }

    /**
     * Resets the global settings to their initial state.
     * This method is marked as `async` to allow for any asynchronous initialization
     * that may be required as part of the reset process.
     */
    static async resetSettings() {
        await this.init();
    }
}