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

/**
 * Handles audio-related functionality, including loading audio files, playing audio, and auto-playing audio.
 */
export class HandlerAudio {

    audio = {};

    /**
     * Automatically plays the specified audio track from the sound list.
     * The audio will play when the page loads, and also when the user clicks on the document once.
     *
     * @param {number} track - The index of the audio track to play in the sound list.
     */
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

    /**
     * Plays the specified audio track from the sound list.
     *
     * @param {number} track - The index of the audio track to play in the sound list.
     */
    static async playAudio(track) {

        const playlist = HandlerGlobalSetting.getSetting('soundList');

        const base64Audio = `data:audio/ogg;base64,${playlist[track]}`;
        const audio = new Audio(base64Audio);

        audio.play().catch(error => {
            console.error('Error trying to play the audio:', error);
        });
    }

    /**
     * Initializes the audio functionality by fetching the audio playlist, and then loading each audio file.
     */
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

    /**
     * Loads an audio file from the server and stores its base64-encoded representation.
     *
     * @param {string} filename - The name of the audio file to load, without the .ogg extension.
     * @returns {Promise<void>} - A Promise that resolves when the audio file has been loaded.
     */
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

    /**
     * Converts a Blob object to a base64-encoded string.
     *
     * @param {Blob} blob - The Blob object to convert.
     * @returns {Promise<string>} - A Promise that resolves with the base64-encoded string.
     */
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

    /**
     * Retrieves the audio file with the specified name.
     *
     * @param {string} name - The name of the audio file to retrieve.
     * @returns {any} - The base64-encoded representation of the audio file.
     */
    getAudio(name) {
        return this.audio[name];
    }

    /**
     * Retrieves all the audio files that have been loaded.
     *
     * @returns {any} - An object containing the base64-encoded representations of the loaded audio files, keyed by their filenames.
     */
    getAllAudio() {
        return this.audio
    }
}