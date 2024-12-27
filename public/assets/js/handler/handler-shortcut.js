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

import {HandlerRequest} from "./handler-request.js";
import {HandlerModal} from "./handler-modal.js";

/**
 * The `HandlerShortcut` class is responsible for managing keyboard shortcuts in the application.
 * It provides methods to initialize the shortcut handler, load shortcuts from the server,
 * register and remove shortcuts, and check if any registered shortcuts match the current key state.
 */
export class HandlerShortcut {

    static shortcuts = new Map();
    
    static keyState = new Set();

    /**
     * An object containing various action methods for the `HandlerShortcut` class.
     */
    static actions = {

        goTranslate: (info) => {

            const config = HandlerModal.initModal();
            config.title = info.title;
            config.content = info.message;
            config.showTitleClose = false;
            config.showFooterClose = false;
            config.backdrop = 'static';
            config.keyboard = false;
            config.type = 'warning';

            config.footerButtons = [
                {
                    text: 'Accept',
                    class: 'btn-primary',
                    onClick: () => {
                        window.location.href = '/Platform-Translator';
                    }
                },
                {
                    text: 'Cancel',
                    class: 'btn-secondary',
                    onClick: () => {
                        const modal = bootstrap.Modal.getInstance(document.querySelector('.modal.show'));
                        modal.hide();
                    }
                }
            ];

            HandlerModal.createModal(config);
        },

        openSearch: (info) => {
            const searchModal = document.getElementById('modal-search');
            if (searchModal) {
                const bsModal = new bootstrap.Modal(searchModal);
                bsModal.show();

                searchModal.addEventListener('shown.bs.modal', () => {
                    const searchInput = searchModal.querySelector('input[type="search"]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }, {once: true});
            }
        }
    };

    /**
     * Initializes the shortcut handler
     * @param {HTMLElement[]} elements - The elements to apply shortcuts to
     */
    static async initialize(elements) {
        this.elements = elements;
        await this.loadShortcuts();
        this.setupEventListeners();
    }

    /**
     * Loads shortcuts from the server
     */
    static async loadShortcuts() {
        try {
            const response = await HandlerRequest.request({
                uri: '/Shortcut',
                method: 'get'
            });
            this.registerShortcut({
                keys: ['Shift', 'Control', 'H'],
                action: () => console.log('Hola esto es un shortcut')
            });
            if (response && response.content.shortcuts) {
                response.content.shortcuts.forEach(shortcut => {
                    this.registerShortcut({
                        keys: shortcut.keys,
                        action: this.actions[shortcut.action],
                        info: {title: shortcut.title, message: shortcut.message}
                    });
                });
            }
        } catch (error) {
            console.error('Error loading shortcuts:', error);
        }
    }

    /**
     * Registers a new shortcut
     * @param {Object} shortcut - Shortcut configuration object
     * @param {string[]} shortcut.keys - Array of key combinations
     * @param {string[]} shortcut.info - Array of string combinations
     * @param {Function} shortcut.action - Action to execute when shortcut is triggered
     */
    static registerShortcut(shortcut) {
        if (!shortcut.action) {
            console.warn('No action defined for shortcut:', shortcut);
            return;
        }
        const key = shortcut.keys.sort().join('+');
        this.shortcuts.set(key, {
            action: shortcut.action,
            info: shortcut.info
        });
    }

    /**
     * Sets up keyboard event listeners
     */
    static setupEventListeners() {
        document.addEventListener('keydown', (event) => {
            this.keyState.add(event.key);
            this.checkShortcuts(event);
        }, {passive: false});

        document.addEventListener('keyup', (event) => {
            event.preventDefault();
            this.keyState.delete(event.key);
        });

        window.addEventListener('blur', (event) => {
            event.preventDefault();
            this.keyState.clear();
        });
    }

    /**
     * Checks if any registered shortcuts match the current key state
     */
    static checkShortcuts(event) {
        const currentKeys = Array.from(this.keyState).sort().join('+');

        for (const [shortcutKeys, shortcutData] of this.shortcuts) {
            if (currentKeys === shortcutKeys) {
                event.preventDefault();
                console.log('Shortcut triggered:', shortcutKeys);
                shortcutData.action(shortcutData.info);
                this.keyState.clear();
                break;
            }
        }
    }

    /**
     * Removes a registered shortcut
     * @param {string[]} keys - Array of keys that make up the shortcut
     */
    static removeShortcut(keys) {
        const key = keys.sort().join('+');
        this.shortcuts.delete(key);
    }

    /**
     * Gets all registered shortcuts
     * @returns {Map} Map of registered shortcuts
     */
    static getShortcuts() {
        return this.shortcuts;
    }
}