<?php

namespace App\Enums;

enum CartStatus: string
{
    case ACTIVE = 'ACTIVE';
    case ABANDONED = 'ABANDONED';
    case CONVERTED = 'CONVERTED';
}
