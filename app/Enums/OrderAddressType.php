<?php

namespace App\Enums;

enum OrderAddressType: string
{
    case SHIPPING = 'SHIPPING';
    case BILLING = 'BILLING';
}
