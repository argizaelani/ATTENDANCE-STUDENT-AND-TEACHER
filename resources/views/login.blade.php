<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Sistem Absensi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-secondary d-flex align-items-center justify-content-center" style="height: 100vh;">
    <div class="card shadow-lg" style="width: 400px;">
        <div class="card-header bg-dark text-white text-center py-3">
            <h4 class="mb-0">Login Administrator</h4>
        </div>
        <div class="card-body p-4">
            @if($errors->any())
                <div class="alert alert-danger">{{ $errors->first() }}</div>
            @endif
            <form action="/login" method="POST">
                @csrf
                <div class="mb-3">
                    <label>Email Akses</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                </div>
                <div class="mb-4">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Masuk ke Sistem</button>
            </form>
        </div>
        <div class="card-footer text-center">
            <a href="/scan" class="text-decoration-none text-muted">Buka Layar Kiosk Scanner</a>
        </div>
    </div>
</body>
</html>