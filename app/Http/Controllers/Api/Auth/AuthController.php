<?php

namespace App\Http\Controllers\Api\Auth;

use App\Enums\SystemRole;
use App\Models\User;
use App\Models\UserOtp;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Api\ApiController;
use App\Http\Resources\Auth\UserResource;
use App\Http\Requests\Auth\RegisterRequest;
use App\Http\Requests\Auth\SendOtpRequest;
use App\Http\Requests\Auth\VerifyOtpRequest;
use App\Http\Requests\Auth\AdminLoginRequest;
use App\Services\Auth\WhatsAppOtpService;

class AuthController extends ApiController
{
    public function __construct(
        protected WhatsAppOtpService $whatsAppOtpService
    ) {
    }

    public function register(RegisterRequest $request): JsonResponse
    {
        $data = $request->validated();

        $user = User::query()->create([
            'name' => $data['name'],
            'email' => $data['email'] ?? null,
            'phone' => $this->normalizePhone($data['phone']),
            'password' => Hash::make($data['password']),
            'locale' => $data['locale'] ?? app()->getLocale(),
            'is_active' => true,
            'account_type' => SystemRole::USER->value,
        ]);

        $user->syncRoles([SystemRole::USER->value]);

        return $this->successResponse([
            'user' => new UserResource($user),
        ], __('messages.registered_successfully'), 201);
    }

    public function sendOtp(SendOtpRequest $request): JsonResponse
    {
        $normalizedPhone = $this->normalizePhone($request->validated()['phone']);

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

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'phone' => __('messages.account_inactive'),
            ]);
        }

        $code = (string) random_int(100000, 999999);

        UserOtp::query()
            ->where('user_id', $user->id)
            ->whereNull('used_at')
            ->delete();

        UserOtp::query()->create([
            'user_id' => $user->id,
            'code' => $code,
            'expires_at' => now()->addMinutes(5),
        ]);

        $this->whatsAppOtpService->send($user->phone, $code);

        return $this->successResponse(
            null,
            __('messages.otp_sent_successfully')
        );
    }

    public function verifyOtp(VerifyOtpRequest $request): JsonResponse
    {
        $validated = $request->validated();
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

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'phone' => __('messages.account_inactive'),
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

        $user->update([
            'last_login_at' => now(),
        ]);

        $token = $user->createToken('user-api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], __('messages.logged_in_successfully'));
    }

    public function adminLogin(AdminLoginRequest $request): JsonResponse
    {
        $credentials = $request->validated();

        $user = User::query()
            ->where('email', $credentials['email'])
            ->first();

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        if (! $user->is_active) {
            throw ValidationException::withMessages([
                'email' => __('messages.account_inactive'),
            ]);
        }

        if (! $user->hasAnyRole([
            SystemRole::SUPER_ADMIN->value,
            SystemRole::ADMIN->value,
        ])) {
            throw ValidationException::withMessages([
                'email' => __('messages.admin_login_only'),
            ]);
        }

        if (! Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'email' => __('messages.invalid_credentials'),
            ]);
        }

        $user->update([
            'last_login_at' => now(),
        ]);

        $token = $user->createToken('admin-api-token')->plainTextToken;

        return $this->successResponse([
            'user' => new UserResource($user),
            'token' => $token,
        ], __('messages.logged_in_successfully'));
    }

    public function logout(Request $request): JsonResponse
    {
        $request->user()?->currentAccessToken()?->delete();

        return $this->successResponse(
            null,
            __('messages.logged_out_successfully')
        );
    }

    public function me(Request $request): JsonResponse
    {
        return $this->successResponse(
            new UserResource($request->user()),
            __('messages.profile_fetched_successfully')
        );
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