<?php
namespace HIEQueryUI;

final class Html {
    private function __construct(private string $markup) {}
    public static function raw(string $markup): self { return new self($markup); }
    public static function escape($text): string {
        return htmlspecialchars((string)$text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
    public static function content($value): string {
        return $value instanceof self ? $value->markup : self::escape($value);
    }
}
