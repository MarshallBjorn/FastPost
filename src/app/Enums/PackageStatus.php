<?php
namespace App\Enums;

enum PackageStatus: string
{
    case REGISTERED = 'registered';
    case IN_TRANSIT = 'in_transit';
    case IN_POSTMAT = 'in_postmat';
    case COLLECTED = 'collected';

    public function label(): string
    {
        return match ($this) {
            self::REGISTERED => 'Registered',
            self::IN_TRANSIT => 'In Transit',
            self::IN_POSTMAT => 'In Postmat',
            self::COLLECTED  => 'Collected',
        };
    }
}
