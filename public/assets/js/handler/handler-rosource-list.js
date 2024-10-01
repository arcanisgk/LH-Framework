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

export class HandlerResourceList {

    static getRequirement() {
        return [
            /*CSS Files Require for System*/
            {typeR: 'css', src: 'assets/lib/animate/animate.min.css'},
            {typeR: 'css', src: 'assets/lib/font-awesome/all.min.css'},
            {typeR: 'css', src: 'assets/lib/jquery-ui/jquery-ui.min.css'},
            {typeR: 'css', src: 'assets/lib/pace/flash.css'},
            {typeR: 'css', src: 'assets/lib/perfect-scrollbar/perfect-scrollbar.css'},

            {typeR: 'css', src: 'assets/lib/sweetalert/sweetalert2.min.css'},

            {typeR: 'css', src: 'assets/lib/theme/main/app.min.css'},

            {typeR: 'css', src: 'assets/css/custom.css'},

            /*JS Files Require for System*/
            {typeR: 'js', src: 'assets/lib/pace/pace.min.js'},
            {typeR: 'js', src: 'assets/lib/jquery/jquery-3.7.1.min.js'},
            {typeR: 'js', src: 'assets/lib/jquery-ui/jquery-ui.min.js'},
            {typeR: 'js', src: 'assets/lib/bootstrap/bootstrap.bundle.min.js'},
            {typeR: 'js', src: 'assets/lib/perfect-scrollbar/perfect-scrollbar.min.js'},
            {typeR: 'js', src: 'assets/lib/js-cookie/js.cookie.min.js'},

            {typeR: 'js', src: 'assets/lib/sweetalert/sweetalert2.min.js'},

            {typeR: 'js', src: 'assets/lib/theme/main/app.min.js'},
            {typeR: 'js', src: 'assets/lib/axios/axios.min.js'},
        ];
    }

    static getCommonPlugin(name) {
        const assets = {
            select2: {
                resources: [
                    {typeR: 'css', src: 'assets/lib/select2/select2.min.css'},
                    {typeR: 'js', src: 'assets/lib/select2/select2.full.min.js'},
                ],
            },
            datatable: {
                resources: [
                    {typeR: 'css', src: 'assets/lib/datatable/datatable.min.css'},
                    {typeR: 'js', src: 'assets/lib/datatable/datatable.min.js'},
                ],
            },
            summernote: {
                resources: [
                    {typeR: 'css', src: 'assets/lib/summernote/summernote-lite.css'},
                    {typeR: 'js', src: 'assets/lib/summernote/summernote-lite.min.js'},
                ],
            },
            dropzone: {
                resources: [
                    {typeR: 'css', src: 'assets/lib/dropzone/dropzone.min.css'},
                    {typeR: 'js', src: 'assets/lib/dropzone/dropzone.full.min.js'},
                ],
            },

        };

        return assets[name]?.resources || null;
    }
}

