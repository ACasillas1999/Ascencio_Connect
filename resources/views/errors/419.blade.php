<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="refresh" content="0;url={{ route('login') }}">
    <title>Sesión Expirada - Ascencio Connect</title>
    <script>
        window.location.href = "{{ route('login') }}";
    </script>
</head>
<body style="background:#020617; color:#fff; display:flex; align-items:center; justify-content:center; height:100vh; font-family:sans-serif; text-align:center;">
    <div>
        <p style="font-size:16px; font-weight:700; color:#fbbf24;">Redirigiendo al inicio de sesión...</p>
        <a href="{{ route('login') }}" style="color:#38bdf8; text-decoration:underline;">Haz clic aquí si no eres redirigido automáticamente</a>
    </div>
</body>
</html>
