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

    /**
     * Checks if a file exists at the given URI.
     *
     * @param {string} uri - The URI of the file to check.
     * @returns {Promise<boolean>} - `true` if the file exists, `false` otherwise.
     */
    static async validateFileExist(uri) {
        try {
            const response = await fetch(uri, {method: 'HEAD'});
            return response.ok;
        } catch (error) {
            console.warn(`File not found: ${uri}`);
            return false;
        }
    }

    /**
     * Sends an HTTP request with the provided configuration.
     *
     * @param {Object} [param={}] - The request parameters.
     * @param {string} [param.uri] - The URI for the request.
     * @param {any} [param.data] - The request data.
     * @param {string} [param.type] - The content type of the request data.
     * @param {string} [param.method] - The HTTP method for the request.
     * @returns {Promise<any>} - The response data.
     */
    static async request(param) {
        const requestHandler = new HandlerRequest();
        return await requestHandler.request(param);
    }

    /**
     * Sends an HTTP request with the provided configuration.
     *
     * @param {Object} [param={}] - The request parameters.
     * @param {string} [param.uri] - The URI for the request.
     * @param {any} [param.data] - The request data.
     * @param {string} [param.type] - The content type of the request data.
     * @param {string} [param.method] - The HTTP method for the request.
     * @returns {Promise<any>} - The response data.
     */
    async request({uri, data, type, method} = {}) {
        try {

            const config = {
                method: method || 'post',
                url: uri,
                baseURL: window.location.origin,
                data: data,
                headers: {
                    'Content-Type': type || 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-UI': true,
                }
            };

            this.setupInterceptors();

            const response = await axios(config);
            return response.data;

        } catch (err) {
            console.error(err);
        }
    }

    /**
     * Sets up interceptors for the Axios HTTP client.
     * The request interceptor restarts the Pace loading indicator.
     * The response interceptor simply passes the response through.
     */
    setupInterceptors() {
        axios.interceptors.request.use(config => {
            Pace.restart();
            return config;
        }, error => Promise.reject(error));

        axios.interceptors.response.use(
            response => response,
            error => Promise.reject(error)
        );
    }
}