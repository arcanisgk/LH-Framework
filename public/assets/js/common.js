import {LoadExternalScripts} from "./loader-script.js";
import {ControllerUI} from "./controller-ui.js";
import {HandlerConsoleOutput} from "./handler/handler-console-output.js";
import {HandlerGlobalSetting} from "./handler/handler-global-setting.js";
import {HandlerInstaller} from "./handler/handler-installer.js";


async function initializeApplication() {
    try {
        await HandlerConsoleOutput.defaultMGS('init');
        await (new LoadExternalScripts()).loadScriptInit();
        HandlerGlobalSetting.Init();
        const controllerUI = new ControllerUI();
        await controllerUI.initializeUI();

        await (new HandlerInstaller()).Init();

        //await HandlerConsoleOutput.debugOut(HandlerGlobalSetting.getAllSettings());

    } catch (error) {
        await HandlerConsoleOutput.defaultMGS('error', 'Deploy Interfaces', error);
    }
}

if (document.readyState !== 'loading') {
    await initializeApplication();
} else {
    document.addEventListener('DOMContentLoaded', initializeApplication);
}
