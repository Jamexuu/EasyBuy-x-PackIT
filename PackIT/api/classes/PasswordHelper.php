<?php
/**
 * PasswordHelper
 *
 * Reusable password complexity validator used by reset/change password processes.
 *
 * Rule: Must be at least 8 characters and include letters and numbers.
 */
class PasswordHelper {
    /**
     * Validate password complexity.
     * Returns true if ok, otherwise returns a human-readable error string.
     *
     * Allowed characters: letters and digits are required; other characters are permitted
     * by the regex below if needed, but the requirement enforces at least one letter and one digit,
     * and a minimum length of 8.
     */
    public static function validate(string $password) {
        if ($password === '') {
            return 'Password is required.';
        }

        // Require at least 8 characters, at least one letter and one digit.
        if (!preg_match('/^(?=.*[A-Za-z])(?=.*\d).{8,}$/', $password)) {
            return 'Password must be at least 8 characters and include letters and numbers.';
        }

        return true;
    }
}