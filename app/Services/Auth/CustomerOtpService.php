<?php

namespace App\Services\Auth;

use App\Models\Customer;
use App\Models\CustomerOtp;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class CustomerOtpService
{
    public function requestOtp(string $phone): void
    {
        $customer = Customer::query()->where('phone', $phone)->first();

        if ($customer === null) {
            throw ValidationException::withMessages([
                'phone' => ['No customer found with this phone number.'],
            ]);
        }

        $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        CustomerOtp::query()->where('phone', $phone)->delete();

        CustomerOtp::query()->create([
            'phone' => $phone,
            'code' => $code,
            'expires_at' => now()->addMinutes(10),
        ]);

        if (config('services.msg91.enabled')) {
            Log::info('Customer OTP requested (MSG91 OTP not configured — code logged for dev)', [
                'phone' => $phone,
                'code' => $code,
            ]);
        } else {
            Log::info('Customer OTP (free mode)', [
                'phone' => $phone,
                'code' => $code,
            ]);
        }
    }

    public function verifyOtp(string $phone, string $code): Customer
    {
        $otp = CustomerOtp::query()
            ->where('phone', $phone)
            ->latest()
            ->first();

        if ($otp === null || $otp->isExpired()) {
            throw ValidationException::withMessages([
                'code' => ['OTP expired or not found. Request a new code.'],
            ]);
        }

        if ($otp->attempts >= 5) {
            throw ValidationException::withMessages([
                'code' => ['Too many attempts. Request a new code.'],
            ]);
        }

        if ($otp->code !== $code) {
            $otp->incrementAttempts();

            throw ValidationException::withMessages([
                'code' => ['Invalid OTP code.'],
            ]);
        }

        $customer = Customer::query()->where('phone', $phone)->firstOrFail();

        $otp->delete();

        return $customer;
    }
}
