<?php
namespace Validation;

use Exception;

class ValidationException extends Exception
{
    public array $errors;

    public function __construct(array $errors)
    {
        parent::__construct("Validation failed");
        $this->errors = $errors;
    }
    
    // AÑADE ESTE MÉTODO
    public function getErrors(): array
    {
        return $this->errors;
    }
}