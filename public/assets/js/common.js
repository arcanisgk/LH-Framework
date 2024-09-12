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

import {ControllerUI} from "./controller-ui.js";
import {HandlerConsoleOutput} from "./handler/handler-console-output.js";
import {HandlerGlobalSetting} from "./handler/handler-global-setting.js";
import {HandlerResourceLoader} from "./handler/handler-resource-loader.js";

await HandlerGlobalSetting.init();
const resourceLoader = new HandlerResourceLoader();
await resourceLoader.loadRequirement();

/**
 * Initializes the application
 *
 * @return {Promise<void>}
 */
async function initApp() {
    try {
        await HandlerConsoleOutput.defaultMGS('init');
        const controllerUI = new ControllerUI();
        await controllerUI.initializeUI();
        await resourceLoader.loadDynamicScript();
        await setLoaderVisibility(false);
    } catch (error) {
        await HandlerConsoleOutput.defaultMGS('error', 'Deploy Interfaces', error);
    }
}

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initApp);
} else {
    await initApp();
}
