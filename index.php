<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="1stile.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            background-image: url('img/imagenfondo.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            height: 100vh;
            margin: 0;
            padding: 0;
            font-family: "Open Sans", -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", Helvetica, Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 80vh;
            flex-direction: column; /* Para alinear verticalmente */
        }

        .card {
            max-width: 400px;
            background-color: black;
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card h1 {
            color: white;
            text-align: center;
            margin-bottom: 50px;
        }

        .card input[type="text"],
        .card input[type="password"],
        .card input[type="email"],
        .card button {
            background-color: #212529;
            color: white;
            border: none;
            margin-bottom: 10px;
            padding: 10px 15px;
            border-radius: 5px;
            width: 100%;
        }

        .card input[type="text"]::placeholder,
        .card input[type="password"]::placeholder,
        .card input[type="email"]::placeholder {
            color: white;
        }

        .card button {
            cursor: pointer;
        }

        .card button:hover {
            background-color: #343a40;
        }

        .alert {
            background-color: #212529;
            color: white;
            border-radius: 5px;
            padding: 10px;
            margin-bottom: 10px;
        }

        .bienvenida {
            text-align: center;
            font-style:italic;
            font-size: 90px;
            margin-bottom: 30px;
            margin-top: 20px; /* Añadido margen superior */
            text-shadow: 18px 18px 18px black;
            color:white;
        }
    </style>
</head>

<body class="fondo">
    <div class="bienvenida">Bienvenido a Sofvigitecol</div>
    <div class="container">
        <div class="card p-4">
            <h1 class="text-center mb-4">Inicio de Sesión</h1>
            <?php
            session_start();

            $_SESSION["formulario_enviado"] = $_SESSION["fomrulario_enviado"] ?? false;

            $errors = []; // Inicializar el array de errores

            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                include_once("./Usuario.php");
                include_once("./conexion.php");

                $user = $_POST["user"] ?? "";
                $pass = $_POST["pass"] ?? "";
                $email = $_POST["email"] ?? "";

                // Validaciones en cada línea del formulario
                if (empty($user)) {
                    $errors[] = "El campo Usuario es requerido";
                }
                if (empty($pass)) {
                    $errors[] = "El campo Contraseña es requerido";
                }
                if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $errors[] = "El campo Correo Electrónico es inválido";
                }

                if (empty($errors)) {
                    $usuario = new Usuario(filter_var($user, FILTER_SANITIZE_FULL_SPECIAL_CHARS), filter_var($pass, FILTER_SANITIZE_FULL_SPECIAL_CHARS), filter_var($email, FILTER_SANITIZE_EMAIL), $conn);

                    if (!$usuario->iniciar_sesion()) {
                        $errors[] = "Clave o Correo Incorrecto";
                    }
                    if (empty($errors)) {
                        $_SESSION["status"] = true;
                        $_SESSION["user"] = filter_var($user, FILTER_SANITIZE_FULL_SPECIAL_CHARS); // Guardar nombre de usuario en sesión
                        $_SESSION["email"] = filter_var($email, FILTER_SANITIZE_EMAIL); // Guardar correo electrónico en sesión
                        header("location: ./home.php");
                        exit();
                    }
                }

                // Mostrar errores de validación
                if (!empty($errors)) {
                    $mensaje_error = implode("<br>", $errors);
                    echo '
                    <script>
                        Swal.fire({
                            title: "Error",
                            text: "' . $mensaje_error . '",
                            icon: "error",
                            confirmButtonText: "Aceptar"
                        });
                    </script>
                    ';
                }
                $_SESSION["formulario_enviado"] = true;
            }

            // Verificar si la variable de sesión indica que el formulario ha sido enviado
            $formulario_enviado = $_SESSION["formulario_enviado"] ?? false;
            if ($formulario_enviado) {
                // Limpiar la variable de sesión
                $_SESSION["formulario_enviado"] = false;
                // Limpia el formulario al cargar la página si el formulario ha sido enviado
                echo '<script>limpiarFormulario();</script>';
            }
            ?>
            <?php if (!empty($mensaje_error)) : ?>
                <div class="alert"><?= $mensaje_error ?></div>
            <?php endif; ?>
            <form id="formulario" action="" method="post">
                <div class="mb-3">
                    <input type="text" name="user" class="form-control" placeholder="Usuario" required>
                </div>
                <div class="mb-3">
                    <input type="password" name="pass" class="form-control" placeholder="Contraseña" required>
                </div>
                <div class="mb-3">
                    <input type="text" name="email" id="email" class="form-control" placeholder="Correo Electrónico"
                        onfocus="this.placeholder=''" onblur="this.placeholder='Correo Electrónico'" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar Sesión</button>
                <button type="button" onclick="limpiarFormulario()" class="btn btn-secondary">Limpiar</button>
            </form>
            <div class="mt-3">
                <p>¿No tienes una cuenta? <a href="./registro.php">Regístrate aquí</a></p>
            </div>
        </div>
    </div>

    <script>
        function limpiarFormulario() {
            document.getElementById("formulario").reset();
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
</body>

</html>
