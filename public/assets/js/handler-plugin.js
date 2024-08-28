export class HandlerPlugin {
    constructor() {
        this.plugins = [];
    }

    registerPlugin(selector, pluginInitCallback) {
        this.plugins.push({selector, init: pluginInitCallback});
    }

    initializePlugins() {
        this.plugins.forEach(plugin => {
            const elements = document.querySelectorAll(plugin.selector);
            elements.forEach(element => plugin.init(element));
        });
    }
}