export class HandlerValidation {
    static validateField(value, rules) {
        if (!rules) return true;

        const {required = false, length = null, lengthLess = null, pattern = null} = rules;

        if (required && (value === null || value.trim() === "")) return false;
        if (length !== null && value.length !== length) return false;
        if (lengthLess !== null && value.length <= lengthLess) return false;
        if (pattern !== null && !pattern.test(value)) return false;

        return true;
    }

    static validateAllFields(modal, fields, values) {
        return fields.every(field => {
            const value = values[field.inputSettingName];
            return this.validateField(value, field.rules);
        });
    }
}