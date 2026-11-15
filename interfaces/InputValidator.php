<?php
namespace HIEQueryUI;

interface InputValidator {
    // Return null to accept the proposed value, or an error message to reject it.
    public function validate(Input $input, string $value): ?string;
}
