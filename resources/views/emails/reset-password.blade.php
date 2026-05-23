@component('mail::message')
# Password Reset Request

You requested a password reset for your account. Please use the following verification code to reset your password:

<h1 style="text-align: center; letter-spacing: 5px;">{{ $resetCode }}</h1>

If you did not request a password reset, no further action is required.

Thanks,<br>
{{ config('app.name') }}
@endcomponent