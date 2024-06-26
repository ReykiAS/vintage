<?php

namespace App\Traits;

use Midtrans\Config;

trait MidtransTrait
{
    protected function initializeMidtrans()
    {
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$clientKey = env('MIDTRANS_CLIENT_KEY');
        Config::$isProduction = false; // Set to true for production
        Config::$isSanitized = true;
        Config::$is3ds = true;
    }
}
