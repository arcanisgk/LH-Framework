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

export class HandlerResourceLoader {

    accelerator = '';//this.getRandomString(13);

    constructor() {
        this.loadedCss = new Set();
        this.loadedJs = new Set();
        this.defaultConfig();
    }

    loadCss(src) {
        return new Promise((resolve, reject) => {
            const link = document.createElement('link');
            link.rel = 'stylesheet';
            link.href = `${src}`;
            link.onload = () => resolve();
            link.onerror = () => reject(new Error(`Failed to load CSS: ${src}`));
            document.head.appendChild(link);
        });
    }

    loadJs({src, defer = true, type = 'text/javascript', async = true} = {}) {
        return new Promise((resolve, reject) => {
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
        });
    }

    async loadResource(resource) {
        if (resource.typeR === 'css' && !this.loadedCss.has(resource.src)) {
            await this.loadCss(resource.src);
            this.loadedCss.add(resource.src);
        } else if (resource.typeR === 'js' && !this.loadedJs.has(resource.src)) {
            await this.loadJs(resource);
            this.loadedJs.add(resource.src);
        }
    }

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

    getScriptPath() {
        const path = window.location.pathname.split('/').pop().toLowerCase();
        const formattedPath = path.replace(/-/g, '');
        return {css: `assets/css/work/${formattedPath}/style.css`, js: `assets/js/work/${formattedPath}/script.js`};
    }

    async loadDynamicScript() {
        const scriptPath = this.getScriptPath();
        try {
            await this.loadCss(scriptPath.css);
            await this.loadJs({src: scriptPath.js, type: 'module'});
        } catch (error) {
            console.warn(`Error loading assets: ${scriptPath}`, error);
        }
    }

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

    defaultConfig() {
        window.paceOptions = {
            eventLag: {
                lagThreshold: 30,
            },
        };
    }
}
