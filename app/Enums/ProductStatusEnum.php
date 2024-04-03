<?php

namespace App\Enums;

enum ProductStatusEnum:string {
    case New = 'new';
    case OnProcess = 'Onprocess';
    case Shipping = 'shipping';
    case Done = 'Done';
}
