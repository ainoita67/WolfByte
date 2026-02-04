class Validator {
    constructor(isValid = false, messageError = "") {
        this.isValid = isValid;
        this.messageError = messageError;
    }

    getisValid() {
        return this.isValid;
    }

    getmessageError() {
        return this.messageError;
    }

    setisValid(isValid) {
        this.isValid = isValid;
    }

    setmessageError(messageError) {
        this.messageError = messageError;
    }

    // Métodos estáticos para validaciones comunes
    static validateRequired(value, fieldName) {
        if (!value || value.trim() === '') {
            return new Validator(false, `El campo ${fieldName} es requerido`);
        }
        return new Validator(true);
    }

    static validateMaxLength(value, maxLength, fieldName) {
        if (value.length > maxLength) {
            return new Validator(false, `${fieldName} no puede exceder ${maxLength} caracteres`);
        }
        return new Validator(true);
    }

    static validatePattern(value, pattern, fieldName, message) {
        if (!pattern.test(value)) {
            return new Validator(false, message || `Formato inválido para ${fieldName}`);
        }
        return new Validator(true);
    }
}

export { Validator };