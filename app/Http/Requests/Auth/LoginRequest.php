<?php

namespace App\Http\Requests\Auth;

use Illuminate\Support\Facades\Auth;
use App\Http\Requests\BaseFormRequest;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class LoginRequest extends BaseFormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Attempt to authenticate the request's credentials.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function authenticate()
    {
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            $this->hitRateLimiter();

            if ($this->is('api/*')) {
                throw new HttpResponseException(
                    $this->validationErrorResponse(['email' => [trans('auth.failed')]])
                );
            }

            $response = redirect()
                ->back()
                ->withInput($this->only('email'))
                ->withErrors(['email' => trans('auth.failed')]);

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ])->redirectTo($response->getTargetUrl());
        }

        /** @var \App\Models\User $user */
        $user = Auth::user();
        if(!$user->isActive()){
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }
        if (!$this->is('api/*') && !$user->isAdmin() && !$user->isManager()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Only admins and managers can access the dashboard.'),
            ]);
        }

        $this->clearRateLimiter();
    }
}
