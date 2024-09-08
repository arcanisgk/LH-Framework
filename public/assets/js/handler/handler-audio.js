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
import {HandlerGlobalSetting} from "./handler-global-setting.js";

export class HandlerAudio {
    audio = {};

    async initialize() {
        try {
            const response = await fetch('/assets/audio/playlist.json');
            if (!response.ok) throw new Error('Failed to fetch audio list');

            const data = await response.json();
            const files = data.files;

            for (const file of files) {
                await this.loadAudioFile(file);
            }

        } catch (error) {
            console.error('Error initializing audio:', error);
        }
    }

    async loadAudioFile(filename) {
        try {

            const response = await fetch(`/assets/audio/${filename}.ogg`);
            if (!response.ok) throw new Error(`Failed to fetch ${filename}.ogg`);

            const blob = await response.blob();
            this.audio[filename] = await this.blobToBase64(blob);

        } catch (error) {
            console.error(`Error loading audio file ${filename}:`, error);
        }
    }

    blobToBase64(blob) {
        return new Promise((resolve, reject) => {
            const reader = new FileReader();
            reader.onloadend = () => {
                const base64 = reader.result.split(',')[1];
                resolve(base64);
            };
            reader.onerror = reject;
            reader.readAsDataURL(blob);
        });
    }

    getAudio(name) {
        return this.audio[name];
    }

    getAllAudio() {
        return this.audio
    }

    static async autoPlayAudio(track) {

        const playlist = HandlerGlobalSetting.getSetting('soundList');

        const base64Audio = `data:audio/ogg;base64,${playlist[track]}`;
        const audio = new Audio(base64Audio);

        const playAudio = () => {
            audio.play().catch(error => {
                console.error('Error trying to play the audio:', error);
            });
        };

        window.addEventListener('load', playAudio);

        document.addEventListener('click', playAudio, {once: true});

    }

    static async playAudio(track) {

        const playlist = HandlerGlobalSetting.getSetting('soundList');

        const base64Audio = `data:audio/ogg;base64,${playlist[track]}`;
        const audio = new Audio(base64Audio);

        audio.play().catch(error => {
            console.error('Error trying to play the audio:', error);
        });
    }
}