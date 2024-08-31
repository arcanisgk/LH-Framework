import {HandlerConsoleOutput} from "./handler/handler-console-output.js";

export class LoadExternalScripts {

    constructor(repository = 'assets/plugins') {
        this.output = new HandlerConsoleOutput();
        this.repository = repository;

        this.scripts = [
            '/datepickk/dist/datepickk.min.js',
            '/gritter/js/jquery.gritter.js',
            '/flot/source/jquery.canvaswrapper.js',
            '/flot/source/jquery.colorhelpers.js',
            '/flot/source/jquery.flot.js',
            '/flot/source/jquery.flot.saturated.js',
            '/flot/source/jquery.flot.browser.js',
            '/flot/source/jquery.flot.drawSeries.js',
            '/flot/source/jquery.flot.uiConstants.js',
            '/flot/source/jquery.flot.time.js',
            '/flot/source/jquery.flot.resize.js',
            '/flot/source/jquery.flot.pie.js',
            '/flot/source/jquery.flot.crosshair.js',
            '/flot/source/jquery.flot.categories.js',
            '/flot/source/jquery.flot.navigate.js',
            '/flot/source/jquery.flot.touchNavigate.js',
            '/flot/source/jquery.flot.hover.js',
            '/flot/source/jquery.flot.touch.js',
            '/flot/source/jquery.flot.selection.js',
            '/flot/source/jquery.flot.symbol.js',
            '/flot/source/jquery.flot.legend.js',
            '/jquery-sparkline/jquery.sparkline.min.js',
            '/d3/d3.min.js',
            '/nvd3/build/nv.d3.min.js',
            '/jvectormap-next/jquery-jvectormap.min.js',
            '/jvectormap-content/world-mill.js',
            '/apexcharts/dist/apexcharts.min.js',
            '/moment/min/moment.min.js',
            '/bootstrap-daterangepicker/daterangepicker.js',
            '/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
            '/jquery-migrate/dist/jquery-migrate.min.js',
            '/tag-it/js/tag-it.min.js',
            '/summernote/dist/summernote-lite.min.js',
            '/clipboard/dist/clipboard.min.js',
            '/highlightjs/cdn-assets/highlight.min.js',
            '/sweetalert2/sweetalert2.all.min.js',
            '/iconify-icon/iconify.min.js',
            '/jstree/dist/jstree.min.js',
            '/intro.js/minified/intro.min.js',
            '/select2/dist/js/select2.min.js',
            '/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
            '/ion-rangeslider/js/ion.rangeSlider.min.js',
            '/jquery.maskedinput/src/jquery.maskedinput.js',
            '/spectrum-colorpicker2/dist/spectrum.min.js',
            '/select-picker/dist/picker.min.js',
            '/switchery/dist/switchery.min.js',
            '/abpetkov-powerange/dist/powerange.min.js',
            '/parsleyjs/dist/parsley.min.js',
            '/axios/axios.min.js',
        ];

        this.scripts = [...new Set(this.scripts)];
    }

    loadScript = (src) => {
        return new Promise((resolve) => {
            const script = document.createElement('script');
            script.src = src;
            script.async = false;
            script.onload = () => resolve({status: 'loaded', src});
            script.onerror = () => resolve({status: 'error', src});
            document.body.appendChild(script);
        });
    };

    async loadScripts() {

        const results = await Promise.all(
            this.scripts.map(src => this.loadScript(this.repository + src))
        );

        const loadedScripts = results.filter(r => r.status === 'loaded').map(r => r.src);
        const failedScripts = results.filter(r => r.status === 'error').map(r => r.src);

        return {
            loaded: loadedScripts,
            failed: failedScripts,
        }
    }

    async loadScriptInit() {

        await this.output.defaultMGS('loader', 'Loading Javascript Assets');

        const result = await this.loadScripts();

        await this.output.defaultMGS('end-loader', `Loaded ${result.loaded.length} out of ${this.scripts.length} Javascript Assets`);

        if (result.failed.length > 0) {
            await this.output.defaultMGS('warning', 'Some scripts failed to load', result.failed);
        }
    }
}