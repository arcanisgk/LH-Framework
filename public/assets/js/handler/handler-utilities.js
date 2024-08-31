export class HandlerUtilities {
    /**
     * @param {string} text - The text to wrap.
     * @param {number} width - The desired total width for the wrapped text.
     * @returns {string} - The wrapped text with right dashes.
     */
    static padRightWithDashes(text, width) {
        if (text.length >= width) {
            return text;
        }
        const paddingLength = width - text.length;
        const padding = '-'.repeat(paddingLength);
        return text + padding;
    }

    static convertDataURIToBinary(dataURI) {
        const BASE64_MARKER = ';base64,';
        const base64Index = dataURI.indexOf(BASE64_MARKER) + BASE64_MARKER.length;
        const base64 = dataURI.substring(base64Index);
        const raw = window.atob(base64);
        const rawLength = raw.length;
        const array = new Uint8Array(rawLength);

        for (let i = 0; i < rawLength; i++) {
            array[i] = raw.charCodeAt(i);
        }

        return array;
    }

    static findModalParent(target) {

        const panelBody = target.closest('.panel-body');
        if (panelBody.length > 0) {
            return {target: panelBody, length: 1};
        }

        const modalBody = target.closest('.modal-body');
        if (modalBody.length > 0) {
            return {target: target.parents('.modal-body'), length: 1};
        }

        return {length: 0};
    }

}

