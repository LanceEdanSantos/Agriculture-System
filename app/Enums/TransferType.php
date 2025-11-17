<?php
    
namespace App\Enums;

enum TransferType:string
{
    case IN = 'in';
    case OUT = 'out';

    public static function options(): array
    {
        return [
            self::IN->value => 'In',
            self::OUT->value => 'Out',
        ];
    }
    
}
