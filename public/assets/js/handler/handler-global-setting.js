import {HandlerAudio} from "./handler-audio.js";
import {HandlerUtilities} from "./handler-utilities.js";

export class HandlerGlobalSetting {

    static settings = {};

    static Init() {


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
            soundClick: HandlerUtilities.convertDataURIToBinary(HandlerAudio.audio.error),
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
        this.Init(); // Reinicia llamando al método Init
    }

}