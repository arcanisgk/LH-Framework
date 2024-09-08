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

export class HandlerGlobalSetting {

    static settings = {};

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

    static getSetting(key) {
        return this.settings[key] ?? null;
    }


    static setSetting(key, value) {
        this.settings[key] = value;
    }


    static getAllSettings() {
        return this.settings;
    }

    static removeSetting(key) {
        delete this.settings[key];
    }

    static resetSettings() {
        this.init(); // Reinicia llamando al método Init
    }

}