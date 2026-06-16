<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SuperMarket - Iniciar Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #1a1924; /* El fondo oscuro de tu barra lateral */
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .card-login {
            background: #ffffff;
            border-radius: 15px;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.3);
            padding: 2.5rem;
            width: 100%;
            max-width: 400px;
        }
        .brand-icon {
            background-color: #e100ff; /* Tu color fucsia brillante */
            color: white;
            width: 60px;
            height: 60px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 1.5rem auto;
        }
        .btn-primary {
            background-color: #e100ff;
            border: none;
            padding: 0.75rem;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background-color: #b800cf;
            transform: translateY(-2px);
        }
        .form-control:focus {
            border-color: #e100ff;
            box-shadow: 0 0 0 0.25rem rgba(225, 0, 255, 0.25);
        }
    </style>
</head>
<body>

<div class="card-login text-center">
    <div class="brand-icon">
        <i class="fas fa-store"></i>
    </div>
    <h3 class="mb-1 fw-bold text-dark">SuperMarket</h3>
    <p class="text-muted mb-4">Ingresa tus credenciales de acceso</p>

    <form action="procesar_login.php" method="POST">
        <div class="mb-3 text-start">
            <label class="form-label text-secondary fw-semibold">Usuario:</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="fas fa-user"></i></span>
                <input type="text" name="usuario" class="form-control" placeholder="Ej. admin" required autocomplete="off">
            </div>
        </div>

        <div class="mb-4 text-start">
            <label class="form-label text-secondary fw-semibold">Contraseña:</label>
            <div class="input-group">
                <span class="input-group-text bg-light text-secondary"><i class="fas fa-lock"></i></span>
                <input type="password" name="clave" class="form-control" placeholder="••••••••" required>
            </div>
        </div>

        <button type="submit" name="ingresar" class="btn btn-primary w-100 rounded-3">
            <i class="fas fa-sign-in-alt me-2"></i> Iniciar Sesión
        </button>
    </form>
</div>

</body>
</html>
