import {HandlerModal} from "../../handler/handler-modal.js";
import {HandlerRequest} from "../../handler/handler-request.js";
import {HandlerTranslateTool} from "../../handler/handler-translate-tool.js";

export class TranslationManager {
    constructor() {
        this.translator = new HandlerTranslateTool();
        this.initialize();
    }

    initialize() {
        document.addEventListener('click', (e) => {
            if (e.target.matches('.translate-btn')) {
                this.handleTranslateButton(e.target);
            }
            if (e.target.matches('.auto-translate-btn')) {
                this.handleAutoTranslate();
            }
        });
    }

    handleTranslateButton(button) {
        const key = button.dataset.key;
        const content = button.dataset.lhContent;
        const translations = this.parseTranslations(content);
        this.showTranslationModal(key, translations, button);
    }

    parseTranslations(content) {
        return content.split(',').reduce((acc, item) => {
            const [lang, text] = item.split(':');
            acc[lang] = text;
            return acc;
        }, {});
    }

    showTranslationModal(key, translations, sourceButton) {
        const languages = ['es', 'en', 'fr', 'pt'];
        const modalContent = this.createModalContent(key, languages, translations);

        const config = HandlerModal.initModal();
        config.title = `Translate: ${key}`;
        config.content = modalContent;
        config.size = 'lg';
        config.type = 'custom';
        config.sourceButton = sourceButton;
        config.footerButtons = [
            {
                text: 'Auto Translate',
                class: 'btn-warning auto-translate-btn',
                onClick: () => this.handleAutoTranslate()
            },
            {
                text: 'Save',
                class: 'btn-primary',
                onClick: () => this.handleSave(key)
            },
            {
                text: 'Approve',
                class: 'btn-success',
                onClick: () => this.handleApprove(key)
            }
        ];

        HandlerModal.showModal(config);
    }

    createModalContent(key, languages, translations) {
        const formGroups = languages.map(lang => `
        <div class="form-group mb-3">
            <label for="translation-${key}-${lang}" class="form-label text-uppercase">${lang}</label>
            <textarea 
                id="translation-${key}-${lang}" 
                class="form-control translation-input" 
                data-lang="${lang}"
                data-key="${key}" 
                rows="3"
            >${translations[lang] || ''}</textarea>
        </div>
    `).join('');

        return `
        <div class="translation-form">
            <input type="hidden" id="translation-key-${key}" value="${key}">
            ${formGroups}
        </div>
    `;
    }

    async handleAutoTranslate() {
        const spanishInput = document.querySelector('.translation-input[data-lang="es"]');
        if (!spanishInput || !spanishInput.value.trim()) {
            await HandlerModal.showErrorModal('Spanish text is required for auto-translation', true);
            return;
        }

        const spanishText = spanishInput.value;
        const targetLanguages = ['en', 'fr', 'pt'];

        try {
            const result = await this.translator.translateText(spanishText, 'es', targetLanguages);

            if (result.success) {
                targetLanguages.forEach(lang => {
                    const translatedText = this.preserveTokens(spanishText, result.translations[lang]);
                    const textarea = document.querySelector(`.translation-input[data-lang="${lang}"]`);
                    if (textarea) {
                        textarea.value = translatedText;
                    }
                });
            } else {
                await HandlerModal.showErrorModal('Auto-translation failed', true);
            }
        } catch (error) {
            await HandlerModal.showErrorModal('Error during auto-translation', true);
        }
    }

    preserveTokens(originalText, translatedText) {
        const tokenRegex = /{{[^}]+}}/g;
        const tokens = originalText.match(tokenRegex) || [];
        let preservedText = translatedText;

        tokens.forEach((token, index) => {
            preservedText = preservedText.replace(new RegExp(`\\{\\{[^}]+\\}\\}*`, 'g'), (match, offset) => {
                return tokens[index] || match;
            });
        });

        return preservedText;
    }


    async handleSave(key) {
        try {
            const translations = this.collectTranslations(key);
            const button = document.querySelector(`.translate-btn[data-key="${key}"]`);

            if (button) {
                button.dataset.lhContent = this.formatTranslationsForButton(translations);
            }

            await this.saveTranslations(key, translations);

            await HandlerModal.showSuccessModal('Translations saved successfully');
            const currentModal = document.querySelector('.modal.show');
            if (currentModal) {
                const bsModal = bootstrap.Modal.getInstance(currentModal);
                bsModal.hide();
            }
        } catch (error) {
            await HandlerModal.showErrorModal('Error saving translations', true);
        }
    }

    async handleApprove(key) {
        try {
            const translations = this.collectTranslations(key);
            await this.approveTranslations(key, translations);

            await HandlerModal.showSuccessModal('Translations approved successfully');
            const currentModal = document.querySelector('.modal.show');
            if (currentModal) {
                const bsModal = bootstrap.Modal.getInstance(currentModal);
                bsModal.hide();
            }
        } catch (error) {
            await HandlerModal.showErrorModal('Error approving translations', true);
        }
    }

    collectTranslations(key) {
        const translations = {};
        document.querySelectorAll('.translation-input').forEach(input => {
            const lang = input.dataset.lang;
            translations[lang] = input.value.trim();
        });
        return translations;
    }

    formatTranslationsForButton(translations) {
        return Object.entries(translations)
            .map(([lang, text]) => `${lang}:${text}`)
            .join(',');
    }

    async saveTranslations(key, translations) {
        return await HandlerRequest.request({
            uri: window.location.pathname,
            method: 'post',
            data: {
                event: 'save-translations',
                key: key,
                translations: translations
            }
        });
    }

    async approveTranslations(key, translations) {
        return await HandlerRequest.request({
            uri: window.location.pathname,
            method: 'post',
            data: {
                event: 'approve-translations',
                key: key,
                translations: translations
            }
        });
    }
}

new TranslationManager();

