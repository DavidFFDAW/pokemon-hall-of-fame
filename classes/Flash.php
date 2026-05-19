<?php

class Flash
{
    private const SESSION_KEY = 'flash_messages';

    public static function add(string $message, string $type = 'info'): void
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            $_SESSION[self::SESSION_KEY] = [];
        }

        $_SESSION[self::SESSION_KEY][] = [
            'message' => $message,
            'type' => $type
        ];
    }

    public static function get(): array
    {
        if (!isset($_SESSION[self::SESSION_KEY])) {
            return [];
        }

        $messages = $_SESSION[self::SESSION_KEY];
        unset($_SESSION[self::SESSION_KEY]);

        return $messages;
    }
}
