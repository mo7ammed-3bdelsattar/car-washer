<?php

namespace App\Http\Requests;

use App\Http\Traits\ApiResponse;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Http\Exceptions\HttpResponseException;

class BaseFormRequest extends FormRequest
{
    use ApiResponse;

    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    public function failedValidation(Validator $validator)
    {
        if ($this->is('api/*')) {
            $response = $this->validationErrorResponse($validator->errors());
            throw new ValidationException($validator, $response);
        }

        $response = redirect()
            ->back()
            ->withInput($this->all())
            ->withErrors($validator);

        throw new ValidationException($validator, $response);
    }

    /**
     * Ensure the request is not rate limited.
     *
     * @throws \Illuminate\Http\Exceptions\HttpResponseException|\Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited(int $maxAttempts = 5, string $attribute = 'email'): void
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($attribute), $maxAttempts)) {
            return;
        }

        event(new Lockout($this));

        $seconds = RateLimiter::availableIn($this->throttleKey($attribute));
        $message = trans('auth.throttle', [
            'seconds' => $seconds,
            'minutes' => ceil($seconds / 60),
        ]);

        if ($this->is('api/*')) {
            throw new HttpResponseException(
                $this->validationErrorResponse([$attribute => $message])
            );
        }

        throw ValidationException::withMessages([
            $attribute => $message,
        ]);
    }

    /**
     * Get the rate limiting throttle key for the request.
     */
    public function throttleKey(string $attribute = 'email'): string
    {
        $value = $this->filled($attribute) ? $this->string($attribute) : '';
        return Str::transliterate(Str::lower($value) . '|' . $this->ip());
    }

    /**
     * Increment the rate limiter attempts.
     */
    public function hitRateLimiter(string $attribute = 'email', int $decaySeconds = 60): void
    {
        RateLimiter::hit($this->throttleKey($attribute), $decaySeconds);
    }

    /**
     * Clear the rate limiter for the request.
     */
    public function clearRateLimiter(string $attribute = 'email'): void
    {
        RateLimiter::clear($this->throttleKey($attribute));
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'required' => __('validation/messages.required'),
            'string' => __('validation/messages.string'),
            'max' => __('validation/messages.max'),
            'min' => __('validation/messages.min'),
            'email' => __('validation/messages.email'),
            'unique' => __('validation/messages.unique'),
            'numeric' => __('validation/messages.numeric'),
            'integer' => __('validation/messages.integer'),
            'date' => __('validation/messages.date'),
            'after' => __('validation/messages.after'),
            'before' => __('validation/messages.before'),
            'after_or_equal' => __('validation/messages.after_or_equal'),
            'before_or_equal' => __('validation/messages.before_or_equal'),
            'confirmed' => __('validation/messages.confirmed'),
            'exists' => __('validation/messages.exists'),
            'in' => __('validation/messages.in'),
            'not_in' => __('validation/messages.not_in'),
            'regex' => __('validation/messages.regex'),
            'size' => __('validation/messages.size'),
            'between' => __('validation/messages.between'),
            'mimes' => __('validation/messages.mimes'),
            'mimetypes' => __('validation/messages.mimetypes'),
        ];
    }
}
