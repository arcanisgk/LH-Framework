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
import {HandlerResourceList} from "./handler-rosource-list.js";
import {HandlerRequest} from "./handler-request.js";

export class HandlerResourceLoader {

    accelerator = '';//this.getRandomString(13);

    /**
     * Initializes the HandlerResourceLoader instance.
     *
     * This constructor sets up the necessary state for the HandlerResourceLoader, including
     * initializing the `loadedCss` and `loadedJs` sets to track the loaded CSS and JavaScript
     * files, and calling the `defaultConfig()` method to set any default configuration.
     */
    constructor() {
        this.loadedCss = new Set();
        this.loadedJs = new Set();
        this.defaultConfig();
    }

    /**
     * Loads a CSS file and tracks its loading status.
     *
     * This method creates a new `<link>` element, sets its `rel` attribute to 'stylesheet' and its `href` attribute to the provided `src` parameter.
     * If the file exists, the link is appended to the `document.head` and the Promise resolves when the CSS has finished loading.
     * If the file does not exist, a warning is logged to the console and the Promise resolves.
     *
     * @param {string} src - The source URL of the CSS file to be loaded.
     * @returns {Promise<void>} A Promise that resolves when the CSS file has been loaded.
     */
    loadCss(src) {
        return new Promise(async (resolve, reject) => {
            let fileExist = await HandlerRequest.validateFileExist(src);
            if (fileExist) {
                const link = document.createElement('link');
                link.rel = 'stylesheet';
                link.href = `${src}`;
                link.onload = () => resolve();
                link.onerror = () => reject(new Error(`Failed to load CSS: ${src}`));
                document.head.appendChild(link);
            } else {
                console.warn(`CSS file not found: ${src}`);
                resolve();
            }
        });
    }

    /**
     * Loads a JavaScript file and tracks its loading status.
     *
     * This method creates a new `<script>` element, sets its `src` attribute to the provided `src` parameter,
     * and optionally sets the `defer` and `async` attributes based on the provided options.
     * If the file exists, the script is appended to the `document.body` and the Promise resolves when the script has finished loading.
     * If the file does not exist, a warning is logged to the console and the Promise resolves.
     *
     * @param {Object} [options={}] - The options object for loading the JavaScript file.
     * @param {string} options.src - The source URL of the JavaScript file to be loaded.
     * @param {boolean} [options.defer=true] - Whether the script should be deferred.
     * @param {string} [options.type='text/javascript'] - The type of the script.
     * @param {boolean} [options.async=true] - Whether the script should be loaded asynchronously.
     * @returns {Promise<void>} A Promise that resolves when the JavaScript file has been loaded.
     */
    loadJs({src, defer = true, type = 'text/javascript', async = true} = {}) {
        return new Promise(async (resolve, reject) => {
            let fileExist = await HandlerRequest.validateFileExist(src);
            if (fileExist) {
                const script = document.createElement('script');
                script.src = `${src}`;
                if (defer) {
                    script.defer = defer;
                }
                if (async) {
                    script.async = async;
                }
                script.type = type;
                script.onload = () => resolve();
                script.onerror = () => reject(new Error(`Failed to load JS: ${src}`));
                document.body.appendChild(script);
            } else {
                console.warn(`JS file not found: ${src}`);
                resolve();
            }
        });
    }

    /**
     * Loads a resource, either a CSS or JavaScript file, and tracks its loading status.
     *
     * If the resource is a CSS file and has not been loaded before, it loads the CSS file and adds the source to the `loadedCss` set.
     * If the resource is a JavaScript file and has not been loaded before, it loads the JavaScript file and adds the source to the `loadedJs` set.
     *
     * @param {Object} resource - The resource object containing information about the file to be loaded.
     * @param {string} resource.typeR - The type of the resource, either 'css' or 'js'.
     * @param {string} resource.src - The source URL of the resource.
     * @returns {Promise<void>} A Promise that resolves when the resource has been loaded.
     */
    async loadResource(resource) {
        if (resource.typeR === 'css' && !this.loadedCss.has(resource.src)) {
            await this.loadCss(resource.src);
            this.loadedCss.add(resource.src);
        } else if (resource.typeR === 'js' && !this.loadedJs.has(resource.src)) {
            await this.loadJs(resource);
            this.loadedJs.add(resource.src);
        }
    }

    /**
     * Loads the required assets for the application.
     *
     * This method retrieves the required assets using the `HandlerResourceList.getRequirement()` method,
     * and then iterates through the assets and loads each one using the `loadResource()` method.
     * If an error occurs during the asset loading, it logs the error to the console.
     *
     * @returns {Promise<void>} A Promise that resolves when all the required assets have been loaded.
     */
    async loadRequirement() {
        const requirementScript = HandlerResourceList.getRequirement();

        try {
            for (const resource of requirementScript) {

                await this.loadResource(resource);
                //console.log('resource Loaded', resource);
            }
        } catch (error) {
            console.error('Error loading assets:', error);
        }
    }

    /**
     * Loads the assets for a given name.
     *
     * This method retrieves the common plugin assets for the specified name using the `HandlerResourceList.getCommonPlugin()` method.
     * If the assets are found, it iterates through them and loads each resource using the `loadResource()` method.
     * If an error occurs during the asset loading, it logs the error to the console.
     * If the assets are not found, it logs a message to the console indicating that the asset was not found.
     *
     * @param {string} name - The name of the assets to load.
     * @returns {Promise<void>} A Promise that resolves when all the assets have been loaded.
     */
    async loadAssets(name) {
        const assetsScript = HandlerResourceList.getCommonPlugin(name);
        if (assetsScript) {
            try {
                for (const resource of assetsScript) {
                    await this.loadResource(resource);
                }
            } catch (error) {
                console.error('Error loading assets:', error);
            }
        } else {
            console.log(`Asset not found: ${name}`);
        }
    }

    /**
     * Generates the script and CSS paths based on the current URL segment.
     *
     * This method extracts the first segment of the URL path, converts it to lowercase, and removes any hyphens.
     * It then constructs the paths for the CSS and JavaScript files based on this formatted segment.
     *
     * @returns {Object} An object containing the CSS and JavaScript file paths.
     */
    getScriptPath() {
        const segment = window.location.pathname.split('/')[1].toLowerCase();
        const formattedPath = segment.replace(/-/g, '');
        return {css: `assets/css/work/${formattedPath}/style.css`, js: `assets/js/work/${formattedPath}/script.js`};
    }

    /**
     * Loads the dynamic script and CSS for the current page.
     *
     * This method retrieves the script and CSS paths based on the current URL segment,
     * and then loads the CSS and JavaScript files asynchronously.
     *
     * If there is an error loading the assets, a warning message is logged to the console.
     */
    async loadDynamicScript() {
        const scriptPath = this.getScriptPath();
        try {
            await this.loadCss(scriptPath.css);
            await this.loadJs({src: scriptPath.js, type: 'module'});
        } catch (error) {
            console.warn(`Error loading assets: ${scriptPath}`, error);
        }
    }

    /**
     * Generates a random string of the specified length.
     *
     * @param {number} length - The length of the random string to generate.
     * @returns {string} A random string of the specified length.
     */
    getRandomString(length) {
        let result = '';
        const characters = 'ABCDEFGHIJKLMNOPQRSTUVWXY#abcdefghilkmnopqrstuvwxyz0123456789';
        const charactersLength = characters.length;
        let counter = 0;
        while (counter < length) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
            counter += 1;
        }
        return result;
    }

    /**
     * Sets the default configuration options for the Pace.js library, which is used to display a loading indicator on the page.
     * The `eventLag` option is set with a `lagThreshold` of 30 milliseconds, which determines the minimum delay before the loading indicator is shown.
     */
    defaultConfig() {
        window.paceOptions = {
            eventLag: {
                lagThreshold: 30,
            },
        };
    }
}
