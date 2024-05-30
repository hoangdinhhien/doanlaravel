<h3>Hi: {{ $account->name }}</h3>
<p>
    Chúc mừng bạn đã tạo tài khoản thành công
</p>

<p>
    <a href="{{ route('account.veryfy', $account->email) }}">Click here to verify your account</a>
</p>
