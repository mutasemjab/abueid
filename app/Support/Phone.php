<?php

namespace App\Support;

/**
 * Jordanian mobile numbers, stored in the local 07XXXXXXXX format.
 */
final class Phone
{
    public const PATTERN = '/^07[789]\d{7}$/';

    /**
     * "+962 79 123 4567", "00962791234567" and "079-123-4567" all become "0791234567".
     */
    public static function normalize(?string $phone): string
    {
        $digits = preg_replace('/[\s-]/', '', (string) $phone);

        return preg_replace('/^(?:\+|00)?962/', '0', $digits);
    }

    /** International format (no "+") for wa.me links. */
    public static function international(string $phone): string
    {
        return preg_replace('/^0/', '962', $phone);
    }
}
