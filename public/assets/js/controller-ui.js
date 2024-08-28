import {HandlerPlugin} from './handler-plugin.js';
import {HandlerEvents} from './handler-event.js';
import {HandlerConsoleOutput} from "./handler-console-output.js";


export class ControllerUI {
    output = new HandlerConsoleOutput();

    constructor() {
        this.pluginInitializer = new HandlerPlugin();
        this.setupPlugins();
        this.formHandler = new HandlerEvents('#myForm');
    }

    elements = [
        '.select2',
        '.datatable',
        '.summernote',
        '.dropzone'
    ];

    setupPlugins() {
        /*
        for(this.elements in selector){
            this.pluginInitializer.registerPlugin(selector, (element) => {
                loadPluginBy(selector);
            });
        }
        */

        this.pluginInitializer.registerPlugin('.select2', (element) => {
            $(element).select2();
        });

        this.pluginInitializer.registerPlugin('.datatable', (element) => {
            $(element).DataTable();
        });

        this.pluginInitializer.registerPlugin('.summernote', (element) => {
            $(element).summernote();
        });

        this.pluginInitializer.registerPlugin('.dropzone', (element) => {
            new Dropzone(element);
        });

    }

    async initializeUI() {

        await this.output.defaultMGS('loader', 'Graphical Interface');
        this.pluginInitializer.initializePlugins();

        //console.log(notDeclaredVar);

        await this.output.defaultMGS('end-loader', 'Graphical Interface');

    }
}