class Validator{

    constructor (isValid = false, messageError = ""){
        this.isValid = isValid;
        this.messageError = messageError;
    }

    getisValid(){
        return this.isValid;
    }

    getmessageError(){
        return this.messageError;
    }

    setisValid(isValid){
        this.isValid = isValid;
    }

    setmessageError(messageError){
        this.messageError = messageError
    }
}

export {Validator}