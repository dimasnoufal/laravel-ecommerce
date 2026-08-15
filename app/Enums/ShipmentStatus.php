<?php

namespace App\Enums;

enum ShipmentStatus: string
{
    case PENDING = 'PENDING';
    case READY_TO_SHIP = 'READY_TO_SHIP';
    case SHIPPED = 'SHIPPED';
    case IN_TRANSIT = 'IN_TRANSIT';
    case DELIVERED = 'DELIVERED';
    case FAILED = 'FAILED';
    case RETURNED = 'RETURNED';
    case CANCELLED = 'CANCELLED';
}
