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

import {HandlerConsoleOutput} from "./handler-console-output.js";
import {HandlerResourceLoader} from "./handler-resource-loader.js";
import {HandlerEvents} from "./handler-event.js";
import {HandlerShortcut} from "./handler-shortcut.js";
import {DatatablePlugin} from "./plugins/pl-datatable.js";
import {Select2Plugin} from "./plugins/pl-select2.js";

/**
 * Represents a plugin manager that handles various UI functionality, such as cache clearing, navigation, password view, spy scroll, Select2, Datatable, SummerNote, Dropzone, password strength, and developer mode.
 * The plugin manager initializes and manages the registered plugins, loading any required assets and executing the plugin's initialization logic.
 */
export class HandlerPlugin {

    output = new HandlerConsoleOutput();
    resources = new HandlerResourceLoader();

    elementSelectors = [
        {
            name: 'keyboard-shortcuts',
            assets: false,
            selector: 'body',
            init: async (elements) => {
                await this.handleKeyboardShortcuts(elements);
            }
        },
        {
            name: 'zoom-control',
            assets: false,
            selector: '[data-lh-pl="zoom-control"]',
            init: async (elements) => {
                await this.handleZoomControl(elements);
            }
        },
        {
            name: 'cache-clear',
            assets: false,
            selector: '[data-lh-pl="cache-clear"]',
            init: async (elements) => {
                await this.handlerCacheClear(elements);
            }
        },
        {
            name: 'navigation',
            assets: false,
            selector: '[data-lh-pl="navigation"]',
            init: async (elements) => {
                await this.handlerNavigation(elements);
            }
        },
        {
            name: 'password',
            assets: false,
            selector: `input[type='password']`,
            init: async (elements) => {
                await this.handlerPasswordView(elements);
            }
        },
        {
            name: 'spy',
            assets: false,
            selector: '[data-lh-pl="spy"]',
            init: async (elements) => {
                await this.handlerSpyScroll(elements);
            }
        },
        {
            name: 'datatable',
            assets: true,
            selector: '[data-lh-pl="datatable"]',
            init: async (elements) => {
                await this.handlerDatatable(elements);
            }
        },
        {
            name: 'select2',
            assets: true,
            selector: '[data-lh-pl="select"]',
            init: async (elements) => {
                await this.handlerSelect2(elements);
            }
        },
        {
            name: 'summernote',
            assets: true,
            selector: '[data-lh-pl="summernote"]',
            init: async (elements) => {
                await this.handlerSummerNote(elements);
            }
        },
        {
            name: 'dropzone',
            assets: true,
            selector: '[data-lh-pl="dropzone"]',
            init: async (elements) => {
                await this.handlerDropZone(elements);
            }
        },
        {
            name: 'password-strength',
            assets: false,
            selector: '[data-lh-pl="password-strength"]',
            init: async (elements) => {
                await this.handlerPasswordStrength(elements);
            }
        },
        {
            name: 'dev-mode',
            assets: false,
            selector: '[data-lh-pl="dev-mode"]',
            init: async (elements) => {
                await this.handlerDevMode(elements);
            }
        },
    ];

    /**
     * Initializes the plugin manager by creating an empty array to store registered plugins.
     */
    constructor() {
        this.plugins = [];
    }

    /**
     * Parses a string of options in the format "key1:value1,key2:value2" and returns an object with the key-value pairs.
     * @param {string} options - The string of options to parse.
     * @returns {Object} An object with the parsed key-value pairs.
     */
    getPlOptions(options) {
        return options.split(',').reduce((obj, par) => {
            const [key, value] = par.split(':').map(element => element.trim());
            obj[key] = value;
            return obj;
        }, {});
    }

    /**
     * Registers a plugin with the plugin manager.
     * @param {Object} plugin - The plugin object to be registered.
     * @returns {void}
     */
    registerPlugin(plugin) {
        this.plugins.push(plugin);
    }

    /**
     * Initializes the registered plugins by finding the corresponding DOM elements and executing the plugin's initialization logic.
     * This method is responsible for loading any required assets, if specified by the plugin, and then calling the plugin's `init` method.
     * After all plugins have been initialized, it will emit a "end-loader" message using the `output.defaultMGS` method.
     *
     * @returns {Promise<void>} A Promise that resolves when all plugins have been initialized.
     */
    async initializePlugins() {
        await this.output.defaultMGS('loader', 'UI Plugin');
        for (const plugin of this.plugins) {
            const elements = document.querySelectorAll(plugin.selector);
            if (elements.length > 0) {
                if (plugin.assets) {
                    await this.resources.loadAssets(plugin.name);
                }
                await plugin.init(elements);
            }
        }
        await this.output.defaultMGS('end-loader', 'UI Plugin');
    }

    /**
     * Handles the spy scroll functionality for a set of elements.
     * This function is triggered when the spy scroll functionality is used.
     * It sets up event handlers for scrolling and clicking on navigation links, and updates the active state of the links based on the current scroll position.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the spy scroll functionality.
     * @returns {Promise<void>}
     */
    async handlerSpyScroll(elements) {
        elements.forEach(element => {

            const target = element.getAttribute('data-spy-target');
            const sections = document.querySelectorAll(`[data-spy-content='${target}'] > div`);
            const navLinks = element.querySelectorAll('a');
            const scrollContainer = document.querySelector(`[data-spy-scroll='${target}']`);

            if (!scrollContainer) return;

            const setActiveLink = link => {
                navLinks.forEach(navLink => navLink.classList.remove("active"));
                navLinks.forEach(navLink => navLink.classList.toggle("active", navLink === link));
            }

            const handleScroll = () => {
                const scrollTop = scrollContainer.scrollTop;
                for (const section of sections) {
                    const sectionTop = section.offsetTop - scrollContainer.offsetTop;
                    const sectionHeight = section.offsetHeight;
                    const link = element.querySelector(`a[href='#${section.id}']`);

                    if (scrollTop + 150 >= sectionTop - 450 && scrollTop < sectionTop + sectionHeight) {
                        setActiveLink(link);
                    }
                }
            }

            const handleClick = (e, link) => {
                e.preventDefault();
                const targetSection = document.querySelector(link.getAttribute('href'));
                if (targetSection) {
                    scrollContainer.scrollTo({
                        top: targetSection.offsetTop - scrollContainer.offsetTop,
                        behavior: 'smooth'
                    });
                }
            }

            scrollContainer.addEventListener('scroll', handleScroll);
            navLinks.forEach(link => link.addEventListener('click', e => handleClick(e, link)));
        });
    }

    /**
     * Handles the password view functionality for a set of elements.
     * This function is triggered when the password view functionality is used.
     * It finds the password input element and the associated eye button, and toggles the password visibility when the button is clicked.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the password view functionality.
     * @returns {Promise<void>}
     */
    async handlerPasswordView(elements) {
        elements.forEach(element => {

            const button = Array.from(element.parentElement.querySelectorAll('button')).find(btn => {
                const icon = btn.querySelector('i');
                return icon && icon.className.includes('fa-eye');
            });

            if (button) {
                let isPasswordVisible = false;

                button.addEventListener('click', function () {

                    if (isPasswordVisible) {
                        element.type = 'password';

                        button.querySelector('i').className = 'fa-solid fa-eye';
                    } else {
                        element.type = 'text';

                        button.querySelector('i').className = 'fa-solid fa-eye-slash';
                    }

                    isPasswordVisible = !isPasswordVisible;
                });
            }

        });
    }

    /**
     * Handles the Select2 functionality for a set of elements.
     * This function is triggered when the Select2 functionality is used.
     * It initializes the Select2 plugin on the provided elements, sets up event handlers, and applies required styling if necessary.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the Select2 functionality.
     * @returns {Promise<void>}
     */
    async handlerSelect2(elements) {
        await Promise.all(Array.from(elements).map(async (element) => {
            const select2Plugin = new Select2Plugin();
            await select2Plugin.initialize(element);
        }));
    }

    /**
     * Handles the Datatable functionality for a set of elements.
     * This function is triggered when the Datatable functionality is used.
     * It logs the provided elements to the console.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the Datatable functionality.
     * @returns {Promise<void>}
     */
    async handlerDatatable(elements) {
        await this.resources.loadAssets('select2');
        await Promise.all(Array.from(elements).map(async (element) => {
            const datatablePlugin = new DatatablePlugin();
            await datatablePlugin.initialize(element);
        }));
    }

    /**
     * Handles the SummerNote functionality for a set of elements.
     * This function is triggered when the SummerNote functionality is used.
     * It logs the provided elements to the console.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the SummerNote functionality.
     * @returns {Promise<void>}
     */
    async handlerSummerNote(elements) {
        console.log(elements);
    }

    /**
     * Handles the drop zone functionality for a set of elements.
     * This function is triggered when the drop zone is used.
     * It logs the provided elements to the console.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the drop zone functionality.
     * @returns {Promise<void>}
     */
    async handlerDropZone(elements) {
        console.log(elements);
    }

    /**
     * Handles the developer mode functionality for a set of elements.
     * This function is triggered when the 'modal-dev-view' modal is shown.
     * It logs a message to the console when the modal becomes visible.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the developer mode functionality.
     * @returns {Promise<void>}
     */
    async handlerDevMode(elements) {
        console.log(elements);
        const modalDevView = document.getElementById('modal-dev-view');
        modalDevView.addEventListener('shown.bs.modal', function () {
            console.log('El modal ya es visible.');
        });
    }

    /**
     * Handles the password strength functionality for a set of elements.
     * This function is triggered by the input event on the password input field.
     * It calculates the password strength based on the configured options and updates the password meter visualization.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the password strength functionality.
     * @returns {Promise<void>}
     */
    async handlerPasswordStrength(elements) {

        const baseWeights = {
            length: 30,
            uppercase: 20,
            lowercase: 20,
            number: 15,
            symbol: 15
        };

        const calculateDynamicWeights = (options) => {
            const activeRules = Object.keys(options).filter(key => options[key] !== 'false' && baseWeights[key]);
            const totalBaseWeight = activeRules.reduce((sum, rule) => sum + baseWeights[rule], 0);

            return activeRules.reduce((weights, rule) => {
                weights[rule] = baseWeights[rule] / totalBaseWeight;
                return weights;
            }, {});
        }

        const calculatePasswordStrength = (password, options) => {
            const dynamicWeights = calculateDynamicWeights(options);

            const criteria = {
                length: password.length >= parseInt(options.length || 0),
                uppercase: options.uppercase === 'true' ? /[A-Z]/.test(password) : true,
                lowercase: options.lowercase === 'true' ? /[a-z]/.test(password) : true,
                number: options.number === 'true' ? /\d/.test(password) : true,
                symbol: options.symbol ? new RegExp(`[${options.symbol}]`).test(password) : true
            };

            let strength = 0;

            for (const [criterion, ismet] of Object.entries(criteria)) {
                if (dynamicWeights[criterion]) {
                    if (ismet) strength += dynamicWeights[criterion];
                }
            }

            return Math.min(strength, 1);
        }

        const updateMeter = (strength, passwordMeter) => {
            const meterSections = passwordMeter.querySelectorAll('.meter-section');
            const strengthClasses = ['weak', 'medium', 'strong', 'very-strong'];
            const strengthIndex = Math.floor(strength * 4);
            const activeSections = Math.ceil(strength * meterSections.length);

            meterSections.forEach((section, index) => {
                section.classList.remove(...strengthClasses);
                if (index < activeSections) {
                    section.classList.add(strengthClasses[Math.min(strengthIndex, 3)]);
                }
            });
        }

        const deployPasswordMeter = sel => {
            const passwordInput = document.getElementById(sel.related);
            const passwordMeter = sel.target;
            const options = this.getPlOptions(sel.options);

            const activeCriteria = Object.keys(options).filter(key => options[key] !== 'false').length;
            const sectionCount = Math.max(activeCriteria * 2, 4); // Ensure at least 4 sections

            for (let i = 0; i < sectionCount; i++) {
                const section = document.createElement('div');
                section.className = 'meter-section rounded me-1';
                passwordMeter.appendChild(section);
            }

            passwordInput.addEventListener('input', () => {
                const password = passwordInput.value;
                const strength = calculatePasswordStrength(password, options);
                updateMeter(strength, passwordMeter);
            });

            updateMeter(0, passwordMeter);
        }

        elements.forEach(element => {
            if (element.dataset.lhPlRelated) {
                let sel = {
                    'target': element,
                    'related': element.dataset.lhPlRelated,
                    'options': element.dataset.lhPlOptions || 'length:13,uppercase:true,lowercase:true,number:true',
                }
                deployPasswordMeter(sel);
            }
        });
    }

    /**
     * Handles the navigation actions for a set of elements.
     * This function is triggered by click or touchstart events on the provided elements.
     * It prevents the default action and performs the appropriate navigation based on the `data-nav-action` attribute of the element.
     * If the `data-nav-action` is 'back', it navigates to the previous page in the browser history.
     * Otherwise, it navigates to the URL specified by the `data-nav-action` attribute.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the navigation when clicked or touched.
     * @returns {Promise<void>}
     */
    async handlerNavigation(elements) {
        elements.forEach(element => {
            const action = element.getAttribute('data-nav-action');
            ['click', 'touchstart'].forEach(eventType => {
                element.addEventListener(eventType, (e) => {
                    e.preventDefault();
                    switch (action) {
                        case 'back':
                            window.history.back();
                            break;
                        default:
                            window.location.href = '/' + action;
                            break;
                    }
                }, {passive: false});
            });
        });
    }

    /**
     * Handles the clearing of the browser cache, local storage, session storage, cookies, and service workers.
     * This function is triggered by clicking on the elements passed in the `elements` parameter.
     * It clears the browser cache, local storage, session storage, cookies, and unregisters any service workers.
     * After the cleanup, it reloads the page with a cache buster to ensure the latest version is loaded.
     *
     * @param {HTMLElement[]} elements - An array of HTML elements that trigger the cache clearing when clicked.
     * @returns {Promise<void>}
     */
    async handlerCacheClear(elements) {
        elements.forEach(element => {
            element.addEventListener('click', async () => {
                localStorage.clear();
                sessionStorage.clear();

                document.cookie.split(";").forEach(cookie => {
                    const name = cookie.split("=")[0].trim();
                    document.cookie = `${name}=; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/;`;
                });

                try {
                    const form = document.createElement('form');
                    form.action = '/Clear-Session';
                    form.method = 'post';

                    const eventInput = document.createElement('input');
                    eventInput.name = 'event';
                    eventInput.value = 'destroy';
                    form.appendChild(eventInput);

                    await HandlerEvents.processEvent({target: eventInput}, form);
                } catch (err) {
                    console.error('Session clearing failed:', err);
                }

                if ('caches' in window) {
                    try {
                        const cacheKeys = await caches.keys();
                        await Promise.all(
                            cacheKeys.map(key => caches.delete(key))
                        );
                    } catch (err) {
                        console.error('Cache clearing failed:', err);
                    }
                }

                if (window.applicationCache) {
                    window.applicationCache.abort();
                }

                if ('serviceWorker' in navigator) {
                    const registrations = await navigator.serviceWorker.getRegistrations();
                    for (let registration of registrations) {
                        await registration.unregister();
                    }
                }

                const cacheBuster = Date.now();
                const url = new URL(window.location.href);
                url.searchParams.set('cache', cacheBuster);

                window.location.reload(true);
            });
        });
    }

    /**
     * Handles the zoom control functionality for the application.
     * @param {HTMLElement[]} elements - The elements to apply the zoom control to.
     * @returns {Promise<void>} - A Promise that resolves when the zoom control has been applied.
     */
    async handleZoomControl(elements) {
        const applyZoom = (isEnabled) => {
            const html = document.documentElement;
            const mainContent = document.querySelector('.app-body');

            if (isEnabled) {

                html.style.transform = 'scale(1.25)';
                html.style.transformOrigin = 'top left';
                html.style.height = '80%';
                html.style.width = '80%';
                html.style.position = 'absolute';

                if (mainContent) {
                    mainContent.style.maxWidth = '80vw';
                    mainContent.style.overflow = 'auto';
                    mainContent.style.height = 'calc(100vh / 1.25)';
                }

                localStorage.setItem('app-zoom-control', 'true');
                document.body.classList.add('zoom-enabled');

                document.body.style.overflow = 'auto';
                document.body.style.position = 'relative';

                sessionStorage.setItem('scrollPos', window.pageYOffset.toString());
            } else {

                html.style.transform = '';
                html.style.transformOrigin = '';
                html.style.height = '';
                html.style.width = '';
                html.style.position = '';

                if (mainContent) {
                    mainContent.style.maxWidth = '';
                    mainContent.style.overflow = '';
                    mainContent.style.height = '';
                }

                localStorage.setItem('app-zoom-control', 'false');
                document.body.classList.remove('zoom-enabled');

                document.body.style.overflow = '';
                document.body.style.position = '';

                const scrollPos = sessionStorage.getItem('scrollPos');
                if (scrollPos) {
                    window.scrollTo(0, parseInt(scrollPos));
                    sessionStorage.removeItem('scrollPos');
                }
            }
        };

        const handleResize = () => {
            const isEnabled = localStorage.getItem('app-zoom-control') === 'true';
            if (isEnabled) {
                applyZoom(true);
            }
        };

        elements.forEach(element => {
            const zoomEnabled = localStorage.getItem('app-zoom-control') === 'true';
            element.checked = zoomEnabled;
            applyZoom(zoomEnabled);

            element.addEventListener('change', (e) => {
                const isEnabled = e.target.checked;
                applyZoom(isEnabled);
            });
        });

        window.addEventListener('resize', handleResize);

        window.addEventListener('beforeunload', () => {
            window.removeEventListener('resize', handleResize);
        });
    }

    /**
     * Initializes keyboard shortcuts for the provided elements.
     * @param {HTMLElement[]} elements - The elements to apply keyboard shortcuts to.
     */
    async handleKeyboardShortcuts(elements) {
        await HandlerShortcut.initialize(elements);
    }
}