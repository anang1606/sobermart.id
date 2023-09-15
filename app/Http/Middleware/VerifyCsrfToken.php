<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        '/api/payments/midtrans-notification',
        '/api/payments/flip-acc-payment',
        '/api/payments/detail-payment',
        '/api/product/*',
        '/api/image/*',
        '/api/vendor/*',
        '/api/manual-payment-bank',
    ];
}