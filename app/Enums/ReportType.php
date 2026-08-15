<?php

namespace App\Enums;

enum ReportType: string
{
    case SALES = 'SALES';
    case INVENTORY = 'INVENTORY';
    case ORDERS = 'ORDERS';
    case PAYMENTS = 'PAYMENTS';
    case CUSTOMERS = 'CUSTOMERS';
}
