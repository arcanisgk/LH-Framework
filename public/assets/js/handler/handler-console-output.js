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

import {HandlerUtilities} from "./handler-utilities.js";

export class HandlerConsoleOutput {

    /**
     * Constructor for HandlerConsoleOutput
     * @param {number} length - The length of the output
     */
    constructor(length = 10) {
        this.length = length;
    }

    /**
     * Static method to create a new instance and call debugOut
     * @param {*} args - The arguments to pass to debugOut
     * @returns {Promise<void>}
     */
    static async debugOut(args) {
        const instance = new HandlerConsoleOutput();
        await instance.debugOut(args);
    }

    /**
     * Debug output method
     * @param {*} args - The arguments to output
     * @returns {Promise<void>}
     */
    async debugOut(args) {
        console.group('LH Javascript Debug Interface');
        await this.out(this.variableAnalyzer(args), 2);
        console.trace('Trace History');
        console.groupEnd();
    }

    /**
     * Default message output method
     * @param {string} select - The type of message to output
     * @param {string} text - The text to output
     * @param {*} error - The error to output
     * @returns {Promise<void>}
     */
    async defaultMGS(select, text = "Message not set", error = null) {

        const messages = {
            'init': async () => await this.out("Welcome to Last Hammer.\n  If you see this message it is located in the browser's command console.", 1),
            'loader': async () => await this.out(this.formatMessage(text, '> Starting'), 2),
            'end-loader': async () => await this.out(this.formatMessage(text, '> Loaded!!!'), 2),
            'error': async () => await this.out(this.formatError(text, error), 3),
        };

        if (messages[select]) {
            await messages[select]();
        } else {
            console.log("Unknown Output");
        }

    }

    /**
     * Static method to create a new instance and call defaultMGS
     * @param {string} select - The type of message to output
     * @param {string} text - The text to output
     * @param {*} error - The error to output
     * @returns {Promise<void>}
     */
    static async defaultMGS(select, text = "Message not set", error = null) {
        const instance = new HandlerConsoleOutput();
        await instance.defaultMGS(select, text, error);
    }

    /**
     * Format a message with a suffix
     * @param {string} text - The text to format
     * @param {string} suffix - The suffix to add
     * @returns {string}
     */
    formatMessage(text, suffix) {
        return text + '\n ' + HandlerUtilities.padRightWithDashes(' ', this.length) + suffix;
    }

    /**
     * Format an error message
     * @param {string} text - The text to format
     * @param {*} error - The error to format
     * @returns {string}
     */
    formatError(text, error) {
        let errorMessage = `${text}\nDetail: Error: not defined`;
        if (error) {
            const details = error.stack || 'No stack trace available';
            errorMessage = `${text}\n ${details}`;
        }
        return `Error: ${errorMessage}`;
    }

    /**
     * Output a message
     * @param {string} msg - The message to output
     * @param {number|string} type - The type of output
     * @returns {Promise<void>}
     */
    async out(msg, type) {
        if (['log', 'info', 'warn', 'error'].includes(type) && typeof console[type] === 'function') {
            console[type](msg);
        } else if ([1, 2, 3].includes(type)) {
            await this.customOutput(msg, type);
        } else {
            throw new TypeError(`Output type not supported: ${type}`);
        }
    }

    /**
     * Custom output method
     * @param {string} msg - The message to output
     * @param {number} type - The type of output
     * @returns {Promise<void>}
     */
    async customOutput(msg, type) {

        let host = window.location.origin;

        const config = {
            1: {imageUrl: `${host}/assets/ico/info.png`, textColor: 'green'},
            2: {imageUrl: `${host}/assets/ico/info.png`, textColor: 'blue'},
            3: {imageUrl: `${host}/assets/ico/error.png`, textColor: 'orange'}
        };

        const {imageUrl, textColor} = config[type] || {};

        if (!imageUrl) {
            console.error("Invalid message type.");
            return;
        }

        const loadImage = (src) => {
            return new Promise((resolve, reject) => {
                const img = new Image();
                img.crossOrigin = "anonymous";
                img.onload = () => resolve(img);
                img.onerror = reject;
                img.src = src;
            });
        };

        try {

            const img = await loadImage(imageUrl);

            const canvas = document.createElement('canvas');
            const ctx = canvas.getContext('2d');

            canvas.width = img.width;
            canvas.height = img.height;
            ctx.fillStyle = 'transparent';
            ctx.fillRect(0, 0, canvas.width, canvas.height);
            ctx.drawImage(img, 0, 0);

            const dataUri = canvas.toDataURL('image/png');

            let iconCSS = `
                padding-left: 8px;
                background-image: url(${dataUri});
                background-repeat: no-repeat;
                background-size: 14px 14px;
                font-size: 12px;
            `;

            const msgCSS = ` 
                text-indent: 7px;
                font-size: 12px;
                color: ${textColor};
            `;

            console.log(`%c %c${msg}`, iconCSS, msgCSS);

        } catch (error) {
            console.error("Error loading image:", error);
        }
    }

    /**
     * Analyze a variable
     * @param {*} value - The value to analyze
     * @returns {string}
     */
    variableAnalyzer(value) {
        const type = typeof value;

        const formatters = {
            object: (val) => {

                if (val === null) {
                    return null;
                }

                if (val instanceof Set || val instanceof Map) {
                    return `${val.constructor.name}: ${JSON.stringify([...val], null, 2)}`;
                }

                if (Array.isArray(val)) {
                    return `Array: ${JSON.stringify(val, null, 2)}`;
                }

                if (typeof val === 'object') {
                    return `Object: ${JSON.stringify(val, null, 2)}`;
                }

                return 'unknown type';
            },
            function: (val) => `Function: ${val.toString()}`,
            symbol: (val) => `Symbol: ${val.toString()}`,
            undefined: () => "undefined",
            string: (val) => `string: ${val}`,
            number: (val) => `number: ${val}`,
            boolean: (val) => `boolean: ${val}`,
        };

        const formatter = formatters[type] || ((val) => `${type}: ${val}`);
        return formatter(value);

    }
}

export default HandlerConsoleOutput;