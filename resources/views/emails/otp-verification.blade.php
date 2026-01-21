@include('emails.email-header')
<div class="email-container">

    <div class="header">
        <img src="{{ $logoPath }}" alt="logo" width="130">
    </div>

    <div class="content">
        <h3>Your Login OTP</h3>
        <p>Hello,</p>
        <p>Your one-time password (OTP) to log in to your <strong>Molfazo</strong> account is:</p>
        <div class="otp">{{ $otp }}</div>
        <p>This OTP is valid for the next 5 minutes.</p>
        <p>If you did not request this, please ignore this email or contact our support immediately.</p>
        <p>Stay healthy,<br>The Molfazo Team</p>
    </div>

</div>
@include('emails.email-footer')
