<?php
namespace App\Enums;

enum PackageSize: string
{
    case S = 'S';
    case M = 'M';
    case L = 'L';

    public function label(): string
    {
        return match ($this) {
            self::S => 'Small',
            self::M => 'Medium',
            self::L => 'Large',
        };
    }
}