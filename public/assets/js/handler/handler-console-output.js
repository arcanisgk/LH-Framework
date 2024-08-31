import {HandlerUtilities} from "./handler-utilities.js";

export class HandlerConsoleOutput {

    constructor(length = 10) {
        this.length = length;
    }

    static async debugOut(args) {
        const instance = new HandlerConsoleOutput();
        await instance.debugOut(args);
    }

    async debugOut(args) {
        console.group('LH Javascript Debug Interface');
        await this.out(this.variableAnalyzer(args), 2);
        console.trace('Trace History');
        console.groupEnd();
    }

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

    static async defaultMGS(select, text = "Message not set", error = null) {
        const instance = new HandlerConsoleOutput();
        await instance.defaultMGS(select, text, error);
    }

    formatMessage(text, suffix) {
        return text + '\n ' + HandlerUtilities.padRightWithDashes(' ', this.length) + suffix;
    }

    formatError(text, error) {
        let errorMessage = `${text}\nDetail: Error: not defined`;
        if (error) {
            const details = error.stack || 'No stack trace available';
            errorMessage = `${text}\n ${details}`;
        }
        return 'Error: ' + errorMessage;
    }

    async out(msg, type) {
        if (['log', 'info', 'warn', 'error'].includes(type) && typeof console[type] === 'function') {
            console[type](msg);
        } else if ([1, 2, 3].includes(type)) {
            await this.customOutput(msg, type);
        } else {
            throw new TypeError(`Output type not supported: ${type}`);
        }
    }

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

    variableAnalyzer(value) {
        const type = typeof value;

        const formatters = {
            'object': (val) => {

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
            'function': (val) => `Function: ${val.toString()}`,
            'symbol': (val) => `Symbol: ${val.toString()}`,
            'undefined': () => "undefined",
            'string': (val) => `string: ${val}`,
            'number': (val) => `number: ${val}`,
            'boolean': (val) => `boolean: ${val}`,
        };

        const formatter = formatters[type] || ((val) => `${type}: ${val}`);
        return formatter(value);

    }
}