<?php


use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\RateLimiter;

RateLimiter::for('otp', function (Request $request) {
    return Limit::perMinute(3)->by($request->input('phone'));
});

RateLimiter::for('register', function (Request $request) {
    return Limit::perMinute(2)->by($request->ip());
});