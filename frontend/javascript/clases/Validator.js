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

// Ejemplo 

/**
    const REGEX_EMAIL = /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    const REGEX_PASSWORD = /^(?=.*[A-Z])(?=.*\d)[A-Za-z\d@$!%*?&]{8,}$/;
      const REGEX_LETRAS_NUM_ESPACIOS = /^[a-zA-Z0-9áéíóúÁÉÍÓÚñÑ\s\-]+$/;
     
    // Le pasaremos todos los datos que querremos validar a la funcion y se hacen las validaciones necesarias
    // y depues se devuelve el validator con el mensaje erroneo si no es valido o sin ningun mensaje si esta todo correcto

    function validarNombreCaracteristica(nombre) { 
        const validator = new Validator();
        
        if (!nombre || nombre.trim() === '') {
            validator.setisValid(false);
            validator.setmessageError("El nombre de la característica es requerido");
            return validator;
        }
        
        if (nombre.length > 100) {
            validator.setisValid(false);
            validator.setmessageError("El nombre no puede exceder los 100 caracteres");
            return validator;
        }
        
        // Validar solo letras, números y espacios
        
        if (!REGEX_LETRAS_NUM_ESPACIOS.test(nombre)) {
            validator.setisValid(false);
            validator.setmessageError("El nombre solo puede contener letras, números, espacios y guiones");
            return validator;
        }
        
        validator.setisValid(true);
        return validator;
    }
*/

/* 
    // Funcion que se encarga de mostrar los mensajes de error en el HTML

    function mostrarAlerta(mensaje, tipo = "info") {
        // Crear contenedor de alertas si no existe
        let alertContainer = document.getElementById('alert-container');
        if (!alertContainer) {
            alertContainer = document.createElement('div');
            alertContainer.id = 'alert-container';
            alertContainer.className = 'position-fixed top-0 end-0 p-3';
            alertContainer.style.zIndex = '1055';
            document.body.appendChild(alertContainer);
        }

        // Crear alerta
        const alertDiv = document.createElement('div');

        alertDiv.className = `alert alert-${tipo} alert-dismissible fade show`;
        alertDiv.role = 'alert';
        alertDiv.innerHTML = `
            ${mensaje}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        alertContainer.appendChild(alertDiv);

        // Auto-eliminar después de 5 segundos
        setTimeout(() => {
            if (alertDiv.parentNode) {
                alertDiv.classList.remove('show');
                setTimeout(() => alertDiv.remove(), 150);
            }
        }, 3000);
    }

*/

/* 

// En la funcion que se maneje el envio de los datos tendremos que poner esto para que salga el mensaje

if (!validator.getisValid()) {
        mostrarAlerta(validator.getmessageError(), "warning");
        return;
    }

*/

export { Validator };