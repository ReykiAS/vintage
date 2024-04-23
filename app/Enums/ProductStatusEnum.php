<?php

namespace App\Enums;

enum ProductStatusEnum:string {
    case New = 'new';
    case OnProcess = 'Onprocess';
    case Shipping = 'shipping';
    case Failed = 'Failed';
    case Done = 'Done';

}
