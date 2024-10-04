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
import {HandlerUtilities} from "./handler-utilities.js";
import {HandlerResourceLoader} from "./handler-resource-loader.js";

export class HandlerPlugin {

    output = new HandlerConsoleOutput();
    resources = new HandlerResourceLoader();

    elementSelectors = [
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
            name: 'select2',
            assets: true,
            selector: '[data-lh-pl="select"]',
            init: async (elements) => {
                await this.handlerSelect2(elements);
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

    constructor() {
        this.plugins = [];
    }

    getPlOptions(options) {
        return options.split(',').reduce((obj, par) => {
            const [key, value] = par.split(':').map(element => element.trim());
            obj[key] = value;
            return obj;
        }, {});
    }

    registerPlugin(plugin) {
        this.plugins.push(plugin);
    }

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

    async handlerSelect2(elements) {

        const destroySelect2 = sel => {
            if (sel.target.data('select2')) {
                sel.target.select2('destroy');
            }
        }

        const deploySelect2 = sel => {

            destroySelect2(sel);
            let extraOptions = {};
            if (sel.options) {
                extraOptions = this.getPlOptions(sel.options);
            }

            let option = {
                width: sel.width,
                dropdownAutoWidth: true,
                ...extraOptions
            }

            let parent = HandlerUtilities.findModalParent(sel.target);
            if (parent.length > 0 && parent.target) {
                option.dropdownParent = parent.target;
            }

            sel.target.select2(option);

            if (sel.required) {
                sel.target.next().children().children().each(function () {
                    $(this).css("border-color", "#f8ac59");
                });
            }
        }

        elements.forEach(element => {
            let target = $(element);
            let sel = {
                'target': target,
                'width': target.attr("data-width") || '100px',
                'required': target.prop('required') || false,
                'options': target.attr("data-lh-pl-options") || false,
            }

            deploySelect2(sel);
        });
    }

    async handlerDatatable(elements) {
        console.log(elements);
    }

    async handlerSummerNote(elements) {
        console.log(elements);
    }

    async handlerDropZone(elements) {
        console.log(elements);
    }

    async handlerDevMode(elements) {
        console.log(elements);
        const modalDevView = document.getElementById('modal-dev-view');
        modalDevView.addEventListener('shown.bs.modal', function () {
            console.log('El modal ya es visible.');
        });
    }

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
}