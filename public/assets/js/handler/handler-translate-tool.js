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

export class HandlerTranslateTool {
    constructor() {
        this.baseUrl = 'http://127.0.0.1:5000/translate';
        this.defaultSourceLang = 'auto';
        this.defaultTargetLangs = ['en', 'fr', 'pt', 'es'];
    }

    async translateText(text, sourceLang = this.defaultSourceLang, targetLangs = this.defaultTargetLangs) {
        const translations = {};

        try {
            for (const targetLang of targetLangs) {
                const response = await fetch(this.baseUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({
                        q: text,
                        source: sourceLang,
                        target: targetLang,
                        format: 'text',
                        alternatives: 3,
                        api_key: ''
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                translations[targetLang] = result.translatedText;
            }

            return {
                success: true,
                originalText: text,
                sourceLang: sourceLang,
                translations: translations
            };

        } catch (error) {
            return {
                success: false,
                error: error.message,
                originalText: text
            };
        }
    }

    setBaseUrl(url) {
        this.baseUrl = url;
    }

    setDefaultSourceLang(lang) {
        this.defaultSourceLang = lang;
    }

    setDefaultTargetLangs(langs) {
        this.defaultTargetLangs = langs;
    }

    getDefaultTargetLangs() {
        return this.defaultTargetLangs;
    }

    getDefaultSourceLang() {
        return this.defaultSourceLang;
    }
}