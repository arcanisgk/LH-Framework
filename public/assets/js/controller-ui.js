import {HandlerPlugin} from './handler/handler-plugin.js';
import {HandlerEvents} from './handler/handler-event.js';
import {HandlerConsoleOutput} from "./handler/handler-console-output.js";


export class ControllerUI {
    output = new HandlerConsoleOutput();
    plugin = new HandlerPlugin();

    constructor() {
        this.setupPlugins();
        //this.formHandler = new HandlerEvents('#myForm');
    }


    setupPlugins() {
        this.plugin.elementSelectors.forEach(plugin => {
            this.plugin.registerPlugin(plugin.selector, plugin.init);
        });
    }

    async initializeUI() {
        await this.output.defaultMGS('loader', 'Graphical Interface');
        await this.plugin.initializePlugins();
        await this.output.defaultMGS('end-loader', 'Graphical Interface');
    }
}