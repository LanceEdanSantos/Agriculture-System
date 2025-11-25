<?php

namespace App\Enums;

use Illuminate\Support\Str;

enum ItemRequestStatus: string 
{
    case PENDING = 'pending';
    case APPROVED = 'approved';
    case REJECTED = 'rejected';
    case FULFILLED = 'fulfilled';

    public function getLabel(): string
    {
        return match ($this) {
            self::PENDING => 'Pending',
            self::APPROVED => 'Approved',
            self::REJECTED => 'Rejected',
            self::FULFILLED => 'Fulfilled',
        };
    }

    public function getColor(): string
    {
        return match ($this) {
            self::PENDING => 'gray',
            self::APPROVED => 'success',
            self::REJECTED => 'danger',
            self::FULFILLED => 'success',
        };
    }

    public function label(): string
    {
        return Str::headline($this->value);
    }
}
