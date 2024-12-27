import {HandlerRequest} from "../../handler/handler-request.js";

export class HandlerTranslator {

    constructor() {
        this.dictionarySelector = document.getElementById('dictionary-selector');
        this.translationsTable = document.getElementById('translations-table');
        this.currentDictionary = null;
        this.dataTable = null;
        this.initialize().then(() => {
            console.log('Translation manager initialized');
        });
    }

    async initialize() {
        try {
            await this.loadDictionaryList();
            this.setupEventListeners();
            this.initializeDataTable();
        } catch (error) {
            console.error('Initialization error:', error);
        }
    }

    setupEventListeners() {
        this.dictionarySelector.addEventListener('change', () => this.loadDictionary());
    }


    initializeDataTable() {

        /*
        this.dataTable = $(this.translationsTable).DataTable({
            columns: [
                {data: 'key', title: 'Key'},
                {
                    data: 'status',
                    title: 'Status',
                    render: (data) => this.renderStatus(data)
                },
                {
                    data: 'priority',
                    title: 'Priority',
                    render: (data) => this.renderPriority(data)
                },
                {
                    data: null,
                    title: 'Translate',
                    render: () => this.renderTranslateButton()
                },
                {
                    data: null,
                    title: 'Report',
                    render: () => this.renderReportButton()
                }
            ],
            responsive: true,
            dom: 'Bfrtip',
            buttons: ['copy', 'excel', 'pdf', 'print']
        });

         */

    }

    async loadDictionaryList() {
        try {
            const response = await HandlerRequest.request({
                uri: window.location.pathname,
                data: {event: 'get-dictionary-list'},
                method: 'post'
            });

            if (response && response.dictionaries) {
                this.populateDictionarySelector(response.dictionaries);
            }
        } catch (error) {
            console.error('Error loading dictionary list:', error);
        }
    }

    populateDictionarySelector(dictionaries) {
        const fragment = document.createDocumentFragment();
        dictionaries.forEach(dict => {
            const option = document.createElement('option');
            option.value = dict.path;
            option.textContent = dict.name;
            fragment.appendChild(option);
        });
        this.dictionarySelector.appendChild(fragment);
    }

    async loadDictionary() {
        const selectedPath = this.dictionarySelector.value;
        if (!selectedPath) return;

        try {
            const response = await HandlerRequest.request({
                uri: window.location.pathname,
                data: {
                    event: 'get-dictionary-content',
                    path: selectedPath
                },
                method: 'post'
            });

            if (response && response.content) {
                this.currentDictionary = response.content;
                this.processTranslationData(response.content);
            }
        } catch (error) {
            console.error('Error loading dictionary content:', error);
        }
    }

    processTranslationData(content) {
        const languages = ['es', 'en', 'fr', 'pt'];
        const tableData = [];
        const allKeys = new Set();

        // Collect all unique keys
        languages.forEach(lang => {
            if (content.translations[lang]) {
                Object.keys(content.translations[lang]).forEach(key => allKeys.add(key));
            }
        });

        // Process each key
        allKeys.forEach(key => {
            const status = this.getTranslationStatus(key, content.translations, languages);
            tableData.push({
                key: key,
                status: status,
                priority: this.getPriorityFromStatus(status),
                translations: this.getTranslationsForKey(key, content.translations, languages)
            });
        });

        this.updateDataTable(tableData);
    }

    getTranslationStatus(key, translations, languages) {
        const missingLanguages = languages.filter(lang =>
            !translations[lang] || !translations[lang][key]
        );

        if (missingLanguages.length === 0) {
            return 'Ready';
        } else if (missingLanguages.length === languages.length) {
            return `No translation in: ${missingLanguages.join(', ')}`;
        } else {
            return `Missing in: ${missingLanguages.join(', ')}`;
        }
    }

    getPriorityFromStatus(status) {
        if (status === 'Ready') return 'none';
        if (status.startsWith('Missing in')) return 'critical';
        return 'high';
    }

    getTranslationsForKey(key, translations, languages) {
        const result = {};
        languages.forEach(lang => {
            result[lang] = translations[lang]?.[key] || '';
        });
        return result;
    }

    updateDataTable(data) {
        this.dataTable.clear();
        this.dataTable.rows.add(data);
        this.dataTable.draw();
    }

    renderStatus(status) {
        return `<span class="badge bg-${this.getStatusBadgeClass(status)}">${status}</span>`;
    }

    renderPriority(priority) {
        const classes = {
            none: 'success',
            critical: 'danger',
            high: 'warning'
        };
        return `<span class="badge bg-${classes[priority]}">${priority}</span>`;
    }

    renderTranslateButton() {
        return '<button class="btn btn-primary btn-sm" onclick="handleTranslate(this)">Translate</button>';
    }

    renderReportButton() {
        return '<button class="btn btn-warning btn-sm" onclick="handleReport(this)">Report</button>';
    }

    getStatusBadgeClass(status) {
        if (status === 'Ready') return 'success';
        if (status.startsWith('Missing in')) return 'danger';
        return 'warning';
    }

    /*
    constructor() {
        this.dictionarySelector = document.getElementById('dictionary-selector');
        this.translationsTable = document.getElementById('translations-table');
        this.filesTable = document.getElementById('files-table');
        this.constantsTable = document.getElementById('constants-table');
        this.saveButton = document.getElementById('save-translations');
        this.saveFixedButton = document.getElementById('save-fixed-sections');
        this.currentDictionary = null;
        this.initialize().then(r => {
            console.log('Initialization complete');
        });
    }

    async initialize() {
        try {
            await this.loadDictionaryList();
            this.setupEventListeners();
        } catch (error) {
            console.error('Initialization error:', error);
        }
    }

    setupEventListeners() {
        this.dictionarySelector.addEventListener('change', () => this.loadDictionary());
        this.saveButton.addEventListener('click', () => this.saveTranslations());
        this.saveFixedButton.addEventListener('click', () => this.saveFixedSections());
    }

    async loadDictionaryList() {
        try {
            const response = await HandlerRequest.request({
                uri: window.location.pathname,
                data: {event: 'get-dictionary-list'},
                method: 'post'
            });

            if (response && response.dictionaries) {
                this.populateDictionarySelector(response.dictionaries);
            }
        } catch (error) {
            console.error('Error loading dictionary list:', error);
        }
    }

    populateDictionarySelector(dictionaries) {
        const fragment = document.createDocumentFragment();
        dictionaries.forEach(dict => {
            const option = document.createElement('option');
            option.value = dict.path;
            option.textContent = dict.name;
            fragment.appendChild(option);
        });
        this.dictionarySelector.appendChild(fragment);
    }

    async loadDictionary() {
        const selectedPath = this.dictionarySelector.value;
        if (!selectedPath) return;

        try {
            const response = await HandlerRequest.request({
                uri: window.location.pathname,
                data: {
                    event: 'get-dictionary-content',
                    path: selectedPath
                },
                method: 'post'
            });

            if (response && response.content) {
                this.currentDictionary = response.content;
                this.renderDictionaryContent(response.content);
            }
        } catch (error) {
            console.error('Error loading dictionary content:', error);
        }
    }

    renderDictionaryContent(content) {
        this.renderTranslationsTable(content.translations);
        this.renderFixedSections(content.fixed);
    }

    renderTranslationsTable(translations) {
        const tbody = this.translationsTable.querySelector('tbody');
        tbody.innerHTML = '';

        const languages = ['es', 'en', 'fr', 'pt'];
        const allKeys = new Set();
        languages.forEach(lang => {
            if (translations[lang]) {
                Object.keys(translations[lang]).forEach(key => allKeys.add(key));
            }
        });

        allKeys.forEach(key => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${key}</td>
                ${languages.map(lang => `
                    <td contenteditable="true" data-lang="${lang}" data-key="${key}">
                        ${translations[lang]?.[key] || ''}
                    </td>
                `).join('')}
            `;
            tbody.appendChild(row);
        });
    }

    renderFixedSections(fixed) {
        this.renderFilesTable(fixed.files || {});
        this.renderConstantsTable(fixed.const || {});
    }

    renderFilesTable(files) {
        const tbody = this.filesTable.querySelector('tbody');
        tbody.innerHTML = '';

        Object.entries(files).forEach(([key, path]) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${key}</td>
                <td contenteditable="true" data-key="${key}">${path}</td>
            `;
            tbody.appendChild(row);
        });
    }

    renderConstantsTable(constants) {
        const tbody = this.constantsTable.querySelector('tbody');
        tbody.innerHTML = '';

        Object.entries(constants).forEach(([key, value]) => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td>${key}</td>
                <td contenteditable="true" data-key="${key}">${value}</td>
            `;
            tbody.appendChild(row);
        });
    }

    async saveTranslations() {
        if (!this.currentDictionary) return;

        const translations = {};
        const cells = this.translationsTable.querySelectorAll('td[contenteditable]');

        cells.forEach(cell => {
            const lang = cell.dataset.lang;
            const key = cell.dataset.key;
            if (!translations[lang]) translations[lang] = {};
            translations[lang][key] = cell.textContent.trim();
        });

        try {
            await this.saveDictionaryContent({
                translations: translations,
                fixed: this.currentDictionary.fixed
            });
        } catch (error) {
            console.error('Error saving translations:', error);
        }
    }

    async saveFixedSections() {
        if (!this.currentDictionary) return;

        const files = {};
        const constants = {};

        this.filesTable.querySelectorAll('td[contenteditable]').forEach(cell => {
            files[cell.dataset.key] = cell.textContent.trim();
        });

        this.constantsTable.querySelectorAll('td[contenteditable]').forEach(cell => {
            constants[cell.dataset.key] = cell.textContent.trim();
        });

        try {
            await this.saveDictionaryContent({
                translations: this.currentDictionary.translations,
                fixed: {
                    files: files,
                    const: constants
                }
            });
        } catch (error) {
            console.error('Error saving fixed sections:', error);
        }
    }

    async saveDictionaryContent(content) {
        try {
            const response = await HandlerRequest.request({
                uri: window.location.pathname,
                data: {
                    event: 'save-dictionary-content',
                    path: this.dictionarySelector.value,
                    content: content
                },
                method: 'post'
            });

            if (response.success) {
                await HandlerResponse.processResponse({
                    content: [{
                        field: 'translator-message',
                        status: 'success',
                        smg: 'Dictionary saved successfully'
                    }]
                });
            }
        } catch (error) {
            console.error('Error saving dictionary:', error);
        }
    }

     */
}

/*
// Initialize the translator
new HandlerTranslator();

// Global handlers for the buttons
window.handleTranslate = function (button) {
    const rowData = $(button).closest('tr').data();
    // Implementation for translation modal
};

window.handleReport = function (button) {
    const rowData = $(button).closest('tr').data();
    // Implementation for report modal
};
*/
