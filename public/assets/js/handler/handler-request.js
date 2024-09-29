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

export class HandlerRequest {


    static async request(param) {
        const requestHandler = new HandlerRequest();
        await requestHandler.request(param);
    }

    async request({uri, data, type, method, output, error} = {}) {
        try {

            const config = {
                method: method || 'post',
                url: uri,
                data: data,
                headers: {
                    'Content-Type': type || 'application/json'
                }
            };

            axios.interceptors.request.use(config => {
                Pace.restart();
                return config;
            }, error => {
                return Promise.reject(error);
            });

            axios.interceptors.response.use(response => {
                return response;
            }, error => {
                return Promise.reject(error);
            });

            const response = await axios(config);

            console.log(response)

            /*
             *
             * aqui se debe implementar la logica de la respuesta
             * mostrar en modal.
             * mostrar el sweet alert.
             * mostrar en el html/DOM
             * Retornar los dato para alimentar un plugin
             *
             */

            /*
            if (response === 'Swal') {
                if (result.data) {

                    Swal.fire({
                        icon: 'success',
                        html: 'Request completed successfully!',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Return'
                    }).then(() => {

                        window.location.href = window.location.origin;

                    });
                } else {
                    // Si hubo un problema con la respuesta
                    Swal.fire({
                        icon: 'warning',
                        html: 'An error occurred!',
                        showCancelButton: true,
                        showConfirmButton: false,
                        cancelButtonText: 'Return'
                    });
                }
            }
            */


        } catch (err) {
            console.error(err);


            // Si hay un error y se especifica manejarlo con SweetAlert
            if (error === 'Swal') {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!'
                });
            }
        }
    }
}