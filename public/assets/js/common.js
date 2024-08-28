import {ControllerUI} from "./controller-ui.js";
import {HandlerConsoleOutput} from "./handler-console-output.js";
import {HandlerGlobalSetting} from "./handler-global-setting.js";

document.addEventListener('DOMContentLoaded', async () => {
    try {
        await HandlerConsoleOutput.defaultMGS('init');
        HandlerGlobalSetting.Init();
        const controllerUI = new ControllerUI();
        await controllerUI.initializeUI();
    } catch (error) {
        await HandlerConsoleOutput.defaultMGS('error', 'Deploy Interfaces', error);
    }
});