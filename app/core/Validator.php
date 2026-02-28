<?php
/**
 * Validator – lightweight input validation.
 *
 * Usage:
 *   $v = new Validator($_POST);
 *   $v->required(['email', 'password'])
 *     ->email('email')
 *     ->min('password', 8);
 *
 *   if ($v->fails()) {
 *       // $v->errors() returns ['field' => 'message', ...]
 *   }
 */

class Validator
{
    private array $data;
    private array $errors = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    // ── Rules ────────────────────────────────────────────────────────

    public function required(array $fields): static
    {
        foreach ($fields as $field) {
            if (!isset($this->data[$field]) || trim((string) $this->data[$field]) === '') {
                $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
            }
        }
        return $this;
    }

    public function email(string $field): static
    {
        if (!empty($this->data[$field]) && !filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field] = 'Please enter a valid email address.';
        }
        return $this;
    }

    public function min(string $field, int $min): static
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) < $min) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must be at least {$min} characters.";
        }
        return $this;
    }

    public function max(string $field, int $max): static
    {
        if (!empty($this->data[$field]) && mb_strlen((string) $this->data[$field]) > $max) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . " must not exceed {$max} characters.";
        }
        return $this;
    }

    public function numeric(string $field): static
    {
        if (!empty($this->data[$field]) && !is_numeric($this->data[$field])) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' must be a number.';
        }
        return $this;
    }

    public function matches(string $field, string $otherField): static
    {
        if (isset($this->data[$field], $this->data[$otherField])
            && $this->data[$field] !== $this->data[$otherField]
        ) {
            $this->errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' does not match.';
        }
        return $this;
    }

    public function in(string $field, array $allowed): static
    {
        if (!empty($this->data[$field]) && !in_array($this->data[$field], $allowed, true)) {
            $this->errors[$field] = 'Invalid value for ' . str_replace('_', ' ', $field) . '.';
        }
        return $this;
    }

    // ── Results ───────────────────────────────────────────────────────

    public function fails(): bool
    {
        return !empty($this->errors);
    }

    public function passes(): bool
    {
        return empty($this->errors);
    }

    /** @return array<string,string> */
    public function errors(): array
    {
        return $this->errors;
    }

    /** Get first error for a field, or null. */
    public function error(string $field): ?string
    {
        return $this->errors[$field] ?? null;
    }
}
