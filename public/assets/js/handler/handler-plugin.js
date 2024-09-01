import {HandlerConsoleOutput} from "./handler-console-output.js";
import {HandlerUtilities} from "./handler-utilities.js";

export class HandlerPlugin {

    output = new HandlerConsoleOutput();

    elementSelectors = [
        {
            selector: '[data-lh-pl="spy"]', init: (elements) => {
                this.handlerSpyScroll(elements);
            }
        },
        {
            selector: '[data-lh-pl="select"]', init: (elements) => {
                this.handlerSelect2(elements);
            }
        },
        {
            selector: '[data-lh-pl="datatable"]', init: (elements) => {
                this.handlerDatatable(elements);
            }
        },
        {
            selector: '[data-lh-pl="summernote"]', init: (elements) => {
                this.handlerSummerNote(elements);
            }
        },
        {
            selector: '[data-lh-pl="dropzone"]', init: (elements) => {
                this.handlerDropZone(elements);
            }
        },
    ];

    constructor() {
        this.plugins = [];
    }

    registerPlugin(selector, pluginInitCallback) {
        this.plugins.push({selector, init: pluginInitCallback});
    }

    async initializePlugins() {
        await this.output.defaultMGS('loader', 'UI Plugin');
        this.plugins.forEach(plugin => {
            const elements = document.querySelectorAll(plugin.selector);
            if (elements.length > 0) {
                plugin.init(elements);
            }
        });
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

    handlerSelect2(elements) {

        const destroySelect2 = sel => {
            if (sel.target.data('select2')) {
                sel.target.select2('destroy');
            }
        }

        const deploySelect2 = sel => {

            destroySelect2(sel);

            let option = {
                width: sel.width,
                dropdownAutoWidth: true,
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
                'required': target.prop('required') || false
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
}