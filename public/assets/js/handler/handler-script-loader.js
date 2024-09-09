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

export class HandlerScriptLoader {
    constructor() {
        this.loadedScripts = new Set();
    }

    async loadScript(src, {defer = true, type = 'text/javascript', async = true} = {}) {
        if (this.loadedScripts.has(src)) {
            //console.log(`Script ${src} already loaded.`);
            return;
        }

        return new Promise((resolve, reject) => {
            const script = document.createElement('script');
            script.src = src;
            script.type = type;
            script.defer = defer;
            script.async = async;

            script.onload = () => {
                this.loadedScripts.add(src);
                //console.log(`Loaded: ${src}`);
                resolve();
            };

            script.onerror = () => {
                reject(new Error(`Failed to load script: ${src}`));
            };

            document.head.appendChild(script);
        });
    }

    async loadMultipleScripts(scripts) {
        const promises = scripts.map(script => {
            if (typeof script === 'string') {
                return this.loadScript(script);
            } else {
                return this.loadScript(script.src, script);
            }
        });

        await Promise.all(promises);
    }

    async loadAllAssets() {

        const requirementScript = [
            {src: 'assets/plugins/vendor/jquery-3.3.1.min.js', defer: false, async: true},

            {src: 'assets/plugins/vendor/pace.min.js', defer: false, async: true},
            {src: 'assets/plugins/vendor/bootstrap.bundle.min.js', defer: false, async: true},
            {src: 'assets/plugins/vendor/jquery-ui.min.js', defer: false, async: true},
            {src: 'assets/plugins/vendor/perfect-scrollbar.min.js', defer: false, async: true},
            {src: 'assets/plugins/vendor/js.cookie.min.js', defer: false, async: true},
            {src: 'assets/plugins/vendor/moment.min.js', defer: false, async: true},

        ];


        const mainPluginScript = [
            {src: 'assets/plugins/jquery-migrate/dist/jquery-migrate.min.js', defer: false, async: true},
            'assets/plugins/datepickk/dist/datepickk.min.js',
            'assets/plugins/gritter/js/jquery.gritter.js',
            'assets/plugins/flot/source/jquery.canvaswrapper.js',
            'assets/plugins/flot/source/jquery.colorhelpers.js',
            'assets/plugins/flot/source/jquery.flot.js',
            'assets/plugins/jquery-sparkline/jquery.sparkline.min.js',
            'assets/plugins/d3/d3.min.js',
            'assets/plugins/jvectormap-next/jquery-jvectormap.min.js',
            'assets/plugins/apexcharts/dist/apexcharts.min.js',
            'assets/plugins/moment/min/moment.min.js',
            'assets/plugins/bootstrap-daterangepicker/daterangepicker.js',
            'assets/plugins/bootstrap-datepicker/dist/js/bootstrap-datepicker.js',
            'assets/plugins/tag-it/js/tag-it.min.js',
            'assets/plugins/summernote/dist/summernote-lite.min.js',
            'assets/plugins/clipboard/dist/clipboard.min.js',
            'assets/plugins/vendor/highlight.min.js',
            'assets/plugins/sweetalert/dist/sweetalert.min.js',
            'assets/plugins/iconify/iconify-icon.min.js',
            'assets/plugins/jstree/dist/jstree.min.js',
            'assets/plugins/intro.js/minified/intro.min.js',
            'assets/plugins/select2/dist/js/select2.min.js',
            'assets/plugins/bootstrap-timepicker/js/bootstrap-timepicker.min.js',
            'assets/plugins/ion-rangeslider/js/ion.rangeSlider.min.js',
            'assets/plugins/jquery.maskedinput/src/jquery.maskedinput.js',
            'assets/plugins/spectrum-colorpicker2/dist/spectrum.min.js',
            'assets/plugins/select-picker/dist/picker.min.js',
            'assets/plugins/switchery/dist/switchery.min.js',
            'assets/plugins/abpetkov-powerange/dist/powerange.min.js',
            'assets/plugins/parsleyjs/dist/parsley.min.js',
        ];

        const extendedPluginScript = [
            'assets/plugins/jvectormap-content/world-mill.js',
            'assets/plugins/flot/source/jquery.flot.saturated.js',
            'assets/plugins/flot/source/jquery.flot.browser.js',
            'assets/plugins/flot/source/jquery.flot.drawSeries.js',
            'assets/plugins/flot/source/jquery.flot.uiConstants.js',
            'assets/plugins/flot/source/jquery.flot.time.js',
            'assets/plugins/flot/source/jquery.flot.resize.js',
            'assets/plugins/flot/source/jquery.flot.pie.js',
            'assets/plugins/flot/source/jquery.flot.crosshair.js',
            'assets/plugins/flot/source/jquery.flot.categories.js',
            'assets/plugins/flot/source/jquery.flot.navigate.js',
            'assets/plugins/flot/source/jquery.flot.touchNavigate.js',
            'assets/plugins/flot/source/jquery.flot.hover.js',
            'assets/plugins/flot/source/jquery.flot.touch.js',
            'assets/plugins/flot/source/jquery.flot.selection.js',
            'assets/plugins/flot/source/jquery.flot.symbol.js',
            'assets/plugins/flot/source/jquery.flot.legend.js',
            'assets/plugins/nvd3/build/nv.d3.min.js',
        ];

        const onExecutionScripts = [
            {src: 'assets/js/theme/app.min.js', defer: false}
        ];

        try {

            await this.loadMultipleScripts(requirementScript);
            jQuery.migrateMute = true;
            await this.loadMultipleScripts(mainPluginScript);
            await this.loadMultipleScripts(extendedPluginScript);
            await this.loadMultipleScripts(onExecutionScripts);

            console.log('All assets loaded successfully');
        } catch (error) {
            console.error('Error loading assets:', error);
        }

    }

    getScriptPath() {
        const path = window.location.pathname.split('/').pop().toLowerCase();
        const formattedPath = path.replace(/-/g, '');
        return `assets/js/work/${formattedPath}/script.js`;
    }

    async loadDynamicScript() {
        const scriptPath = this.getScriptPath();
        try {
            await this.loadScript(scriptPath, {type: 'module', defer: false, async: false});
            console.log(`Script Loaded Successfully: ${scriptPath}`);
        } catch (error) {
            console.warn(`Error loading assets: ${scriptPath}`, error);
        }
    }

    async loadExtraScript($directory) {
        await this.loadMultipleScripts($directory);
    }
}
