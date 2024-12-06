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


class HandlerSw {
    constructor() {
        this.initialize();
    }

    initialize() {
        self.addEventListener('fetch', this.handleFetch.bind(this));
    }

    handleFetch(event) {
        if (event.request.method === 'HEAD') {
            event.respondWith(
                fetch(event.request).catch(() => new Response('', {status: 404}))
            );
        }
    }
}

new HandlerSw();