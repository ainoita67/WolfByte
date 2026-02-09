<?php
namespace Validation;

class ValidationException extends \Exception
{
    private array $errors;

    public function __construct(array $errors)
    {
        parent::__construct("Validation failed"); // mensaje general
        $this->errors = $errors;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}
