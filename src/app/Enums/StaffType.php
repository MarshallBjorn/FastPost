<?php

namespace App\Enums;

enum StaffType: string
{
    case ADMIN = 'admin';
    case POSTMAT_COURIER = 'postmat_courier';
    case WAREHOUSE_COURIER = 'warehouse_courier';
    case WAREHOUSE = 'warehouse';

    public function label(): string
    {
        return match ($this) {
            self::ADMIN => 'admin',
            self::POSTMAT_COURIER => 'postmat_courier',
            self::WAREHOUSE_COURIER => 'warehouse_courier',
            self::WAREHOUSE => 'warehouse',
        };
    }
}
