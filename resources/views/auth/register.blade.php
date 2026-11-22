<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Daftar — iStore</title>
</head>
<body>
    <main>
        <h1>Daftar Akun Pembeli</h1>

        @if ($errors->any())
            <div role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div>
                <label for="name">Nama Lengkap</label>
                <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name">
            </div>

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autocomplete="username">
            </div>

            <div>
                <label for="phone">Nomor Telepon (Opsional)</label>
                <input id="phone" type="text" name="phone" value="{{ old('phone') }}" autocomplete="tel">
            </div>

            <div>
                <label for="address">Alamat Pengiriman (Opsional)</label>
                <textarea id="address" name="address" rows="3">{{ old('address') }}</textarea>
            </div>

            <div>
                <label for="password">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="new-password">
            </div>

            <div>
                <label for="password_confirmation">Konfirmasi Kata Sandi</label>
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
            </div>

            <button type="submit">Daftar</button>
        </form>

        <p>Sudah punya akun? <a href="{{ route('login') }}">Masuk</a></p>
    </main>
</body>
</html>
