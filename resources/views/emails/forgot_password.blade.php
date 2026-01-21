@include('emails.email-header')

<div class="email-container">

    <div class="header">
        <img src="{{ $logoPath }}" alt="logo" width="130">
    </div>

    <div class="content">
        <h3>Your New Password</h3>

        <p>Hello {{ $name ?? 'User' }},</p>

        <p>
            You requested to reset your <strong>Molfazo</strong> account password.
            Below is your new password:
        </p>

        <div class="otp">
            {{ $password }}
        </div>

        <p>
            Please enter and confirm this password in the app to activate it.
        </p>

        <p>
            This password is valid for the next <strong>10 minutes</strong>.
        </p>

        <p>
            If you did not request this password reset, please ignore this email
            or contact our support team immediately.
        </p>

        <p>
            Stay healthy,<br>
            <strong>The Molfazo Team</strong>
        </p>
    </div>

</div>

@include('emails.email-footer')
