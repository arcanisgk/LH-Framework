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
import {HandlerResourceList} from "./handler-resource-list.js";
import {HandlerRequest} from "./handler-request.js";

export class HandlerResourceLoader {

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
        if (this.loadedCss.has(src)) {
            return Promise.resolve();
        }

        return new Promise(async (resolve, reject) => {
            const fileExist = await HandlerRequest.validateFileExist(src);
            if (!fileExist) {
                console.warn(`CSS file not found: ${src}`);
                resolve();
                return;
            }

            const preloadLink = document.createElement('link');
            preloadLink.rel = 'preload';
            preloadLink.as = 'style';
            preloadLink.href = src;
            preloadLink.crossOrigin = 'anonymous';
            document.head.appendChild(preloadLink);

            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = src;
            link.media = 'print';
            link.crossOrigin = 'anonymous';

            link.onload = () => {
                link.media = 'all';
                this.loadedCss.add(src);
                resolve();
            };
            link.onerror = () => reject(new Error(`Failed to load CSS: ${src}`));
            document.head.appendChild(link);
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
        if (this.loadedJs.has(src)) {
            return Promise.resolve();
        }

        return new Promise(async (resolve, reject) => {
            const fileExist = await HandlerRequest.validateFileExist(src);
            if (!fileExist) {
                console.warn(`JS file not found: ${src}`);
                resolve();
                return;
            }

            const preloadLink = document.createElement('link');
            preloadLink.rel = 'preload';
            preloadLink.as = 'script';
            preloadLink.href = src;
            preloadLink.crossOrigin = 'anonymous';
            document.head.appendChild(preloadLink);

            const script = document.createElement('script');
            script.src = src;
            script.defer = defer;
            script.async = async;
            script.type = type;
            script.crossOrigin = 'anonymous';

            script.onload = () => {
                this.loadedJs.add(src);
                resolve();
            };
            script.onerror = () => reject(new Error(`Failed to load JS: ${src}`));
            document.body.appendChild(script);
        });
    }

    /**
     * Loads a group of resources, including CSS and JavaScript files, in a specific order.
     *
     * This method first groups the resources by their group number, then loads the groups sequentially while loading the resources within each group in parallel. For CSS resources, it calls the `loadCss()` method, and for JavaScript resources, it calls the `loadJs()` method.
     *
     * @param {Object[]} resources - An array of resource objects, each with properties like `group`, `typeR`, and `src`.
     * @returns {Promise<void>} A Promise that resolves when all the resources have been loaded.
     */
    async loadResourcesByGroup(resources) {

        const groupedResources = resources.reduce((acc, resource) => {
            const group = resource.group || 1;
            if (!acc[group]) acc[group] = [];
            acc[group].push(resource);
            return acc;
        }, {});

        const groups = Object.keys(groupedResources).sort((a, b) => a - b);
        for (const group of groups) {
            const groupResources = groupedResources[group];
            await Promise.all(
                groupResources.map(resource => {
                    if (resource.typeR === 'css') {
                        return this.loadCss(resource.src);
                    } else {
                        return this.loadJs(resource);
                    }
                })
            );
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
            await this.loadResourcesByGroup(requirementScript);
        } catch (error) {
            console.error('Error loading requirements:', error);
            throw error;
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
        if (!assetsScript) {
            console.log(`Asset not found: ${name}`);
            return;
        }

        try {
            await this.loadResourcesByGroup(assetsScript);
        } catch (error) {
            console.error('Error loading assets:', error);
            throw error;
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
        return {
            css: `assets/css/work/${formattedPath}/style.css`,
            js: `assets/js/work/${formattedPath}/script.js`
        };
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
            await this.loadResourcesByGroup([
                {typeR: 'css', group: 1, src: scriptPath.css},
                {typeR: 'js', group: 2, src: scriptPath.js, type: 'module'}
            ]);
        } catch (error) {
            console.warn(`Error loading dynamic script:`, error);
            throw error;
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
