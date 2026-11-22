<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — iStore</title>
</head>
<body>
    <main>
        <h1>Masuk ke iStore</h1>

        @if ($errors->any())
            <div role="alert">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf

            <div>
                <label for="email">Email</label>
                <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
            </div>

            <div>
                <label for="password">Kata Sandi</label>
                <input id="password" type="password" name="password" required autocomplete="current-password">
            </div>

            <div>
                <label for="remember">
                    <input id="remember" type="checkbox" name="remember" value="1" @checked(old('remember'))>
                    Ingat saya
                </label>
            </div>

            <button type="submit">Masuk</button>
        </form>

        <p>Belum punya akun? <a href="{{ route('register') }}">Daftar sebagai Pembeli</a></p>
    </main>
</body>
</html>
