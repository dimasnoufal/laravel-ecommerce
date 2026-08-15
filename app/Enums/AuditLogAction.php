<?php

namespace App\Enums;

enum AuditLogAction: string
{
    case CREATE = 'CREATE';
    case UPDATE = 'UPDATE';
    case DELETE = 'DELETE';
    case LOGIN = 'LOGIN';
    case LOGOUT = 'LOGOUT';
    case PAYMENT_PAID = 'PAYMENT_PAID';
    case STOCK_DEDUCTED = 'STOCK_DEDUCTED';
    case ORDER_CANCELLED = 'ORDER_CANCELLED';
    case REVIEW_APPROVED = 'REVIEW_APPROVED';
}
