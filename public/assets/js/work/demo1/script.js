import {HandlerResourceLoader} from "../../handler/handler-resource-loader.js";

async function loadExtraScript() {
    const demoScript = [
        {src: 'assets/plugins/jquery-migrate/dist/jquery-migrate.min.js', defer: false, async: true},
        {src: 'assets/demo/dashboard.js', defer: false, async: true},
        {src: 'assets/demo/dashboard-v2.js', defer: false, async: true},
        {src: 'assets/demo/dashboard-v3.js', defer: false, async: true},
        {src: 'assets/demo/email-inbox.demo.js', defer: false, async: true},
        {src: 'assets/demo/email-compose.demo.js', defer: false, async: true},
        {src: 'assets/demo/widget.demo.js', defer: false, async: true},
        {src: 'assets/demo/render.highlight.js', defer: false, async: true},
        {src: 'assets/demo/ui-modal-notification.demo.js', defer: false, async: true},
        {src: 'assets/demo/ui-tree.demo.js', defer: false, async: true},

    ];
    await (new HandlerResourceLoader()).loadExtraScript(demoScript);
}


await loadExtraScript();