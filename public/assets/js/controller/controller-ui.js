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

import {HandlerPlugin} from '../handler/handler-plugin.js';
import {HandlerEvents} from '../handler/handler-event.js';
import {HandlerConsoleOutput} from "../handler/handler-console-output.js";


export class ControllerUI {
    output = new HandlerConsoleOutput();
    plugin = new HandlerPlugin();
    event = new HandlerEvents();

    /**
     * Class constructor.
     *
     * Sets up the plugins.
     *
     * @return {void}
     */
    constructor() {
        this.setupPlugins();
    }

    /**
     * Sets up the plugins.
     *
     * Loops through all the registered plugins and calls the `registerPlugin`
     * method on each of them.
     *
     * @return {void}
     */
    setupPlugins() {
        this.plugin.elementSelectors.forEach(plugin => {
            this.plugin.registerPlugin(plugin);
        });
    }

    /**
     * Initializes the graphical interface.
     *
     * Calls the `initializePlugins` method on the `HandlerPlugin` instance.
     *
     * @async
     * @return {Promise<void>}
     */
    async initializeUI() {
        await this.output.defaultMGS('loader', 'Graphical Interface');
        await this.event.initializeEvents();
        await this.plugin.initializePlugins();
        await this.output.defaultMGS('end-loader', 'Graphical Interface');
    }

}