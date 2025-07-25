<?php

namespace App\Helpers;

class FormatHelper
{
    public static function toSom(int|string $coins): float|int
    {
        return 1 * $coins / 100;
    }

    public static function toCoins(float $amount): int
    {
        return round(1 * $amount * 100);
    }

    public static function timestamp(bool $milliseconds = false): float|int
    {
        if ($milliseconds) {
            return round(microtime(true)) * 1000; // milliseconds
        }

        return time(); // seconds
    }

    public static function timestamp2seconds(int $timestamp): int
    {
        // is it already as seconds
        if (strlen((string)$timestamp) == 10) {
            return $timestamp;
        }

        return floor(1 * $timestamp / 1000);
    }

    public static function timestamp2milliseconds(int $timestamp): float|int
    {
        // is it already as milliseconds
        if (strlen((string)$timestamp) == 13) {
            return $timestamp;
        }

        return $timestamp * 1000;
    }

    public static function timestamp2datetime(int $timestamp): string
    {
        if (strlen((string)$timestamp) == 13) {
            $timestamp = self::timestamp2seconds($timestamp);
        }

        return date('Y-m-d H:i:s', $timestamp);
    }

    public static function datetime2timestamp(?string $datetime): float|int|string
    {
        if(!$datetime) {
            return $datetime;
        }
        return 1000 * strtotime($datetime);
    }
}
