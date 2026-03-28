<?php

namespace App\Http\Controllers\Web\Auth;

use App\Enums\SystemRole;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use App\Services\Auth\WhatsAppOtpService;

class OtpLoginController extends Controller
{
    public function __construct(
        protected WhatsAppOtpService $whatsAppOtpService
    ) {
    }

    public function showRequestForm(): View
    {
        return view('auth.login');
    }

  //  public function sendCode(Request $request): RedirectResponse
    //{
      //  $validated = $request->validate([
        //    'phone' => ['required', 'string', 'max:30'],
        //]);

        //$normalizedPhone = $this->normalizePhone($validated['phone']);

       // $user = User::query()
         //   ->get()
           // ->first(function (User $user) use ($normalizedPhone) {
           //     return $this->normalizePhone($user->phone) === $normalizedPhone;
            //});

        //if (! $user || ! $user->hasRole(SystemRole::USER->value)) {
          //  throw ValidationException::withMessages([
               // 'phone' => __('messages.user_not_found'),
            //]);
        //}

        //if (! $user->is_active) {
          //  throw ValidationException::withMessages([
            //    'phone' => __('messages.account_inactive'),
            //]);
        //}

        //$code = (string) random_int(100000, 999999);

      //  UserOtp::query()
        //    ->where('user_id', $user->id)
          //  ->whereNull('used_at')
          //  ->delete();

        //UserOtp::query()->create([
          //  'user_id' => $user->id,
            //'code' => $code,
           // 'expires_at' => now()->addMinutes(5),
        //]);

        //$this->whatsAppOtpService->send($user->phone, $code);

        //return redirect()
          //  ->route('otp.verify.form', ['phone' => $user->phone])
            //->with(
              //  'success',
                //app()->getLocale() === 'ar'
                  //  ? 'تم إرسال رمز التحقق إلى واتساب.'
                    //: 'Verification code has been sent to WhatsApp.'
            //);
    //}
public function sendCode(Request $request): RedirectResponse
{
    $request->validate([
        'phone' => ['required', 'string', 'max:30'],
    ]);

    $user = User::query()->first();

    if (! $user) {
        throw ValidationException::withMessages([
            'phone' => app()->getLocale() === 'ar'
                ? 'ما في أي مستخدم بقاعدة البيانات.'
                : 'No users found in database.',
        ]);
    }

    Auth::login($user);
    $request->session()->regenerate();

    $user->update([
        'last_login_at' => now(),
    ]);

    return redirect()
        ->route('home')
        ->with(
            'success',
            app()->getLocale() === 'ar'
                ? 'تم تسجيل الدخول مؤقتًا بدون OTP.'
                : 'Logged in temporarily without OTP.'
        );
}
    public function showVerifyForm(Request $request): View
    {
        return view('auth.otp-verify', [
            'phone' => $request->query('phone'),
        ]);
    }

    public function verifyCode(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'phone' => ['required', 'string', 'max:30'],
            'code' => ['required', 'string', 'size:6'],
        ]);

        $normalizedPhone = $this->normalizePhone($validated['phone']);

        $user = User::query()
            ->get()
            ->first(function (User $user) use ($normalizedPhone) {
                return $this->normalizePhone($user->phone) === $normalizedPhone;
            });

        if (! $user || ! $user->hasRole(SystemRole::USER->value)) {
            throw ValidationException::withMessages([
                'phone' => __('messages.user_not_found'),
            ]);
        }

        $otp = UserOtp::query()
            ->where('user_id', $user->id)
            ->where('code', $validated['code'])
            ->latest()
            ->first();

        if (! $otp || ! $otp->isValid()) {
            throw ValidationException::withMessages([
                'code' => __('messages.otp_invalid_or_expired'),
            ]);
        }

        $otp->update([
            'used_at' => now(),
        ]);

        Auth::login($user);
        $request->session()->regenerate();

        $user->update([
            'last_login_at' => now(),
        ]);

        return redirect()->route('home');
    }

    private function normalizePhone(?string $phone): string
    {
        $phone = trim((string) $phone);
        $phone = preg_replace('/\s+/', '', $phone);
        $phone = str_replace(['-', '(', ')'], '', $phone);

        if (str_starts_with($phone, '+963')) {
            $phone = '0' . substr($phone, 4);
        } elseif (str_starts_with($phone, '963')) {
            $phone = '0' . substr($phone, 3);
        } elseif (str_starts_with($phone, '00963')) {
            $phone = '0' . substr($phone, 5);
        }

        return $phone;
    }
}