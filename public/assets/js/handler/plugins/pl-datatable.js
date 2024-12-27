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

import {HandlerModal} from "../handler-modal.js";
import {HandlerPlugin} from "../handler-plugin.js";


export class DatatablePlugin {

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
     * Renders a DataTable on the provided element.
     * @param {HTMLElement} element - The DOM element to render the DataTable on.
     * @returns {Promise} A Promise that resolves to the DataTable instance.
     */
    async renderDatatable(element) {

        /**
         * The `tableConfig` object is a configuration object that manages the initialization and rendering of a DataTable on a given DOM element. It provides the following functionality:
         *
         * - `getDatatableConfig()`: Retrieves the configuration object for the DataTable, including the target element and any options specified in the element's `data-lh-pl-options` attribute.
         * - `getDefaultTableConfig()`: Returns the default configuration options for the DataTable, including settings for paging, ordering, searching, scrolling, and more.
         * - `parseOptions()`: Parses a string of options in the format "key1:value1,key2:value2" and merges them with the default configuration options.
         * - `setupEventHandlers()`: Attaches event handlers to the DataTable instance, such as a handler for the `draw.dt` event that triggers a `datatable:update` event on the table container.
         * - `createExportModal()`: Creates a modal dialog for exporting the DataTable data to PDF or Excel formats, allowing the user to customize the export options.
         * - `getButtons()`: Returns an array of button configurations for the DataTable, including buttons for copying, printing, exporting to PDF/Excel, and toggling column visibility.
         * - `getDomLayout()`: Returns the default DOM layout for the DataTable, including the placement of the length menu, search box, table, and pagination controls.
         */
        const tableConfig = {

            config: {},

            getDatatableConfig: () => {

                const config = {
                    target: $(element),
                    options: element.getAttribute("data-lh-pl-options") || false
                };

                if (config.options) {
                    this.config = this.getPlOptions(config.options);
                }

                return config;
            },

            getDefaultTableConfig: () => ({
                landscape: false,
                paging: false,
                ordering: false,
                info: false,
                searching: false,
                autoWidth: false,
                scrollContainer: false,
                scrollX: false,
                scrollY: false,
                responsive: false,
                columnDefs: [
                    {targets: '_all', width: 'auto'}
                ],
                fixedColumns: {
                    leftColumns: false,
                    rightColumns: false,
                },
                colReorder: false,
                dom: false,
                buttons: false,
                order: [],
                iDisplayLength: -1,
                language: {
                    decimal: '.',
                    thousands: ',',
                    emptyTable: '<i class="fa-solid fa-comment-slash"></i>'.repeat(3),
                    info: '_START_ <i class="fa-solid fa-arrow-right"></i> _END_ || _TOTAL_',
                    infoEmpty: '0 <i class="fa-solid fa-arrow-right"></i> 0 || 0',
                    infoFiltered: ' [ T: _MAX_ ]',
                    infoPostFix: "",
                    lengthMenu: "_MENU_",
                    loadingRecords: '<i class="fa-solid fa-arrow-right"></i>'.repeat(2) + ' ...',
                    processing: '<i class="fa-solid fa-arrow-right"></i>'.repeat(2) + ' ...',
                    search: '<i class="fa-solid fa-magnifying-glass"></i>',
                    searchPlaceholder: '',
                    zeroRecords: "---",
                    paginate: {
                        first: '<i class="fa-solid fa-angles-left"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>',
                        next: '<i class="fas fa-chevron-right"></i>',
                        last: '<i class="fa-solid fa-angles-right"></i>'
                    }
                },
                lengthMenu: [
                    [5, 10, 25, 50, 100, 250, 500, 1000, -1],
                    [5, 10, 25, 50, 100, 250, 500, 1000, "∞"]
                ],
                initComplete: function (settings) {
                    const api = new $.fn.dataTable.Api(settings);
                    api.page('last').draw('page');
                },
                drawCallback: function (settings) {
                    const api = new $.fn.dataTable.Api(settings);
                    const handlerPlugin = new HandlerPlugin();
                    const table = $(this);
                    const jqueryElements = table.find('select[data-lh-pl="select-dt"]');
                    const nativeElements = jqueryElements.get();
                    if (nativeElements.length > 0) {
                        handlerPlugin.handlerSelect2(nativeElements);
                        jqueryElements.on('select2:select', function (e) {
                            api.columns.adjust();
                        });
                    }
                }
            }),

            createExportModal: (type, e, dt, node, config, cb) => {

                const modalConfig = HandlerModal.initModal();
                modalConfig.title = `Export to ${type}`;

                const landscape = this.config.landscape || false;

                const recommendedSettings = {
                    orientation: landscape ? 'landscape' : 'portrait',
                    pageSize: (() => {
                        if (landscape && dt.columns(':visible')[0].length > 12) return 'LEGAL';
                        return 'LETTER';
                    })()
                };

                const defaultFilename = this.config.filename || 'export';
                const recommendedOrientation = recommendedSettings.orientation;
                const recommendedSize = recommendedSettings.pageSize;

                modalConfig.content = type.toLowerCase() === 'pdf' ? `
                    <div class="form-group mb-3">
                        <label for="filename" class="form-label">Filename</label>
                        <input type="text" class="form-control" id="filename" value="${defaultFilename}">
                    </div>
                    <div class="form-group mb-3">
                        <label for="orientation" class="form-label">Page Orientation:</label>
                        <select class="form-select" id="orientation">
                            <option value="portrait" ${recommendedOrientation === 'portrait' ? 'selected' : ''}>Portrait</option>
                            <option value="landscape" ${recommendedOrientation === 'landscape' ? 'selected' : ''}>Landscape</option>
                        </select>
                    </div>
                    <div class="form-group mb-3">
                        <label for="pageSize" class="form-label">Page Size:</label>
                        <select class="form-select" id="pageSize">
                            <option value="LETTER" ${recommendedSize === 'LETTER' ? 'selected' : ''}>Letter</option>
                            <option value="LEGAL" ${recommendedSize === 'LEGAL' ? 'selected' : ''}>Legal</option>
                            <option value="A4" ${recommendedSize === 'A4' ? 'selected' : ''}>A4</option>
                            <option value="A3" ${recommendedSize === 'A3' ? 'selected' : ''}>A3</option>
                        </select>
                    </div>` : `
                    <div class="form-group">
                        <label for="filename" class="form-label">Filename</label>
                        <input type="text" class="form-control" id="filename" value="${defaultFilename}">
                    </div>`;

                const exportConfig = {
                    pdf: {
                        action: 'pdfHtml5',
                        extraConfig: {
                            customize: function (doc) {
                                doc.defaultStyle.fontSize = 8;
                                doc.styles.tableHeader.fontSize = 9;
                                doc.styles.tableHeader.alignment = 'left';
                                doc.content[1].table.widths =
                                    Array(doc.content[1].table.body[0].length + 1).join('*').split('');
                            }
                        }
                    },
                    excel: {
                        action: 'excelHtml5',
                        extraConfig: {}
                    }
                };

                modalConfig.footerButtons = [
                    {
                        text: 'Export',
                        class: 'btn-info',
                        onClick: () => {
                            const filename = document.getElementById('filename').value || defaultFilename;
                            const exportType = exportConfig[type.toLowerCase()];
                            const orientation = document.getElementById('orientation')?.value || recommendedOrientation;
                            const pageSize = document.getElementById('pageSize')?.value || recommendedSize;

                            console.log({'orientation': orientation, 'pageSize': pageSize});

                            const exportOptions = {
                                ...config,
                                filename: filename,
                                ...exportType.extraConfig,
                                exportOptions: {
                                    columns: ':visible',
                                    orthogonal: 'export'
                                }
                            };

                            if (type.toLowerCase() === 'pdf') {
                                exportOptions.orientation = orientation;
                                exportOptions.pageSize = pageSize;
                            }

                            $.fn.dataTable.ext.buttons[exportType.action].action.call(
                                this,
                                e,
                                dt,
                                node,
                                exportOptions,
                                cb
                            );

                            bootstrap.Modal.getInstance(document.querySelector('.modal.show')).hide();

                        }
                    }
                ];

                HandlerModal.createModal(modalConfig);
            },

            getButtons: function () {
                return [{
                    extend: 'collection',
                    text: '<i class="fas fa-cog"></i>',
                    className: 'btn btn-sm btn-info d-flex align-items-center',
                    collectionLayout: 'three-column',
                    autoClose: true,
                    buttons: [
                        {
                            extend: 'copyHtml5',
                            text: '<i class="fas fa-copy"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                            exportOptions: {columns: ':visible'}
                        },
                        {
                            extend: 'print',
                            text: '<i class="fas fa-print"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                            exportOptions: {columns: ':visible'}
                        },
                        {
                            extend: 'pdfHtml5',
                            text: '<i class="fa-solid fa-file-pdf"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                            action: function (e, dt, node, config, cb) {
                                tableConfig.createExportModal('PDF', e, dt, node, config, cb);
                            }
                        },
                        {
                            extend: 'colvis',
                            text: '<i class="fas fa-eye-slash"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                        },
                        {
                            extend: 'excelHtml5',
                            text: '<i class="fas fa-file-excel"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                            action: function (e, dt, node, config, cb) {
                                tableConfig.createExportModal('Excel', e, dt, node, config, cb);
                            }
                        },
                        {
                            text: '<i class="fas fa-question-circle"></i>',
                            className: 'btn fs-4 text-center table-export-element',
                            action: function (e, dt, node, config) {
                                // Implement help action
                            }
                        }
                    ]
                }];
            },

            getDomLayout: () => (
                '<"d-flex justify-content-between align-items-center mb-1"' +
                '<"d-flex align-items-center"l<"html5buttons d-flex flex-row"B>>f>' +
                'rt' +
                '<"d-flex flex-column flex-sm-row justify-content-between align-items-center gap-2"ip>'
            ),

            parseOptions: (options, baseOptions) => {
                if (!options) return baseOptions;

                const optionsObj = this.getPlOptions(options);

                Object.entries(optionsObj).forEach(([key, value]) => {
                    if (key === 'dom') {
                        baseOptions[key] = tableConfig.getDomLayout();
                    } else if (key === 'leftColumns' || key === 'rightColumns') {

                        baseOptions['fixedColumns'][key] = value;

                    } else if (key === 'buttons' && value === 'true') {
                        baseOptions[key] = tableConfig.getButtons();
                    } else {
                        baseOptions[key] = value === 'true' ? true :
                            value === 'false' ? false :
                                !isNaN(value) ? Number(value) : value;
                    }
                });

                return baseOptions;
            },

            setupEventHandlers: (datatable) => {
                datatable.on('draw.dt', function () {
                    const info = datatable.page.info();
                    $(datatable.table().container()).trigger('datatable:update', {
                        page: info.page + 1,
                        pages: info.pages,
                        recordsTotal: info.recordsTotal
                    });
                });
            }

        }

        /**
         * Initializes and renders the datatable for the given element.
         * @returns {Promise<DataTable>} - A Promise that resolves to the initialized DataTable instance.
         */
        const init = () => {

            const config = tableConfig.getDatatableConfig();
            const baseOptions = tableConfig.getDefaultTableConfig();
            const options = tableConfig.parseOptions(config.options, baseOptions);
            const datatable = config.target.DataTable(options).columns.adjust();
            tableConfig.setupEventHandlers(datatable);

            return datatable;
        }

        await init();

    }

    /**
     * Initializes and renders the datatable for the given element.
     * @param {HTMLElement} element - The DOM element to render the datatable in.
     * @returns {Promise<DataTable>} - A Promise that resolves to the initialized DataTable instance.
     */
    async initialize(element) {
        return await this.renderDatatable(element);
    }
}