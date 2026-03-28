<?php

namespace App\Services\Auth;

use Illuminate\Support\Facades\Log;

class WhatsAppOtpService
{
    public function send(string $phone, string $code): void
    {
        /*
         |--------------------------------------------------------------
         | مؤقتًا للتجربة:
         | خزّن الرسالة في log بدل الإرسال الحقيقي.
         | لاحقًا استبدل هذا الجزء بربط Twilio أو Meta WhatsApp API
         |--------------------------------------------------------------
         */

        Log::info('WhatsApp OTP', [
            'phone' => $phone,
            'code' => $code,
            'message' => "Your verification code is: {$code}",
        ]);
    }
}