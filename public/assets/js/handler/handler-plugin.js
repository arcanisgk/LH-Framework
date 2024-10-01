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
            init: (elements) => {
                this.handlerPasswordView(elements);
            }
        },
        {
            name: 'spy',
            assets: false,
            selector: '[data-lh-pl="spy"]',
            init: (elements) => {
                this.handlerSpyScroll(elements);
            }
        },
        {
            name: 'select2',
            assets: true,
            selector: '[data-lh-pl="select"]',
            init: (elements) => {
                this.handlerSelect2(elements);
            }
        },
        {
            name: 'datatable',
            assets: true,
            selector: '[data-lh-pl="datatable"]',
            init: (elements) => {
                this.handlerDatatable(elements);
            }
        },
        {
            name: 'summernote',
            assets: true,
            selector: '[data-lh-pl="summernote"]',
            init: (elements) => {
                this.handlerSummerNote(elements);
            }
        },
        {
            name: 'dropzone',
            assets: true,
            selector: '[data-lh-pl="dropzone"]',
            init: (elements) => {
                this.handlerDropZone(elements);
            }
        },
        {
            name: 'dev-mode',
            assets: false,
            selector: '[data-lh-pl="dev-mode"]',
            init: (elements) => {
                this.handlerDevMode(elements);
            }
        },
    ];

    constructor() {
        this.plugins = [];
    }

    getPlOptions(options) {
        return options.split(', ').reduce((obj, par) => {
            const [key, value] = par.split(': ').map(element => element.trim());
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
                plugin.init(elements);
            }
        }
        await this.output.defaultMGS('end-loader', 'UI Plugin');
    }

    handlerSpyScroll(elements) {
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

    handlerPasswordView(elements) {
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

    handlerSelect2(elements) {

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

    handlerDatatable(elements) {
        console.log(elements);
    }

    handlerSummerNote(elements) {
        console.log(elements);
    }

    handlerDropZone(elements) {
        console.log(elements);
    }

    handlerDevMode(elements) {
        console.log(elements);
        const modalDevView = document.getElementById('modal-dev-view');
        modalDevView.addEventListener('shown.bs.modal', function () {
            console.log('El modal ya es visible.');
        });
    }


}