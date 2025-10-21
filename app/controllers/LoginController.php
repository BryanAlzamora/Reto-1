<?php
require_once __DIR__ . '/BaseController.php';
require_once __DIR__ . '/../models/UsuarioModel.php';


class LoginController extends BaseController {

    public function login() {
        session_start();

        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        $usuarioModel = new UsuarioModel();
        $usuario = $usuarioModel->getUsuarioLogin($email, $password); // método existente en UsuarioModel

        if ($usuario) {
            $_SESSION['user'] = [
                'id'     => $usuario->ID_Usuario,
                'nombre' => $usuario->Username,
                'email'  => $usuario->Email,
                'tipo'   => $usuario->Tipo
            ];
        } else {
            $_SESSION['error'] = 'Usuario o contraseña incorrectos';
        }

        $this->redirect('?controller=InicioController');
    }

    public function logout() {
        session_start();   // 👈 Muy importante
        session_destroy();
        $this->redirect('?controller=InicioController');
    }


    public function register() {
        session_start();

        $tipoCuenta = $_POST['tipoCuenta'] ?? '';
        $nombre = $_POST['nombre'] ?? '';
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        $cif = $_POST['cif'] ?? null;

        $usuarioModel = new UsuarioModel();

        // Verificar tipo de cuenta
        if (strtolower($tipoCuenta) === 'comprar') {
            $tipoCuenta = 'Cliente';
        } elseif (strtolower($tipoCuenta) === 'vender') {
            $tipoCuenta = 'Comerciante';
        } else {
            $_SESSION['error'] = 'Debes seleccionar un tipo de cuenta';
            $this->redirect('?controller=InicioController');
            exit;
        }

        // 🚨 Verificar si ya existe el usuario (por email, por ejemplo)
        if ($usuarioModel->getUsuarioByEmail($email)) {
            $_SESSION['error'] = 'Ya existe un usuario registrado con ese correo';
            $this->redirect('?controller=InicioController');
            exit;
        }

        // Crear nuevo usuario
        $datos = [
            'cif' => $cif,
            'username' => $nombre,
            'password' => $password,
            'email' => $email,
            'tipo' => $tipoCuenta
        ];

        if ($usuarioModel->create($datos)) {
            $_SESSION['user'] = [
                'nombre' => $nombre,
                'email' => $email,
                'tipo' => $tipoCuenta
            ];
        } else {
            $_SESSION['error'] = 'Error al registrar el usuario';
        }

        $this->redirect('?controller=InicioController');
    }
}
