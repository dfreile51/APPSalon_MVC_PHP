<?php

namespace Controllers;

use Model\Servicio;
use MVC\Router;

class ServicioController
{
    public static function index(Router $router)
    {
        iniciarSession();

        isAdmin();

        $servicios = Servicio::all();
        $alertas = [];

        $router->render('servicios/index', [
            'nombre' => $_SESSION['nombre'],
            'servicios' => $servicios,
            'alertas' => $alertas
        ]);
    }

    public static function crear(Router $router)
    {
        iniciarSession();

        isAdmin();

        $servicio = new Servicio();
        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/crear', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function actualizar(Router $router)
    {
        iniciarSession();

        isAdmin();

        // debuguear(filter_var($_GET['id'], FILTER_VALIDATE_INT));
        if (!filter_var($_GET['id'], FILTER_VALIDATE_INT)) return;

        $servicio = Servicio::find($_GET['id']);

        if (!$servicio) {
            header('Location: /servicios');
        }

        $alertas = [];

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $servicio->sincronizar($_POST);

            $alertas = $servicio->validar();

            if (empty($alertas)) {
                $servicio->guardar();
                header('Location: /servicios');
            }
        }

        $router->render('servicios/actualizar', [
            'nombre' => $_SESSION['nombre'],
            'servicio' => $servicio,
            'alertas' => $alertas
        ]);
    }

    public static function eliminar(Router $router)
    {
        iniciarSession();

        isAdmin();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!filter_var($_POST['id'], FILTER_VALIDATE_INT)) return;

            $servicio = Servicio::find($_POST['id']);

            if (!$servicio) {
                Servicio::setAlerta('error', 'Servicio no encontrado');

                $alertas = Servicio::getAlertas();

                $router->render('servicios/index', [
                    'nombre' => $_SESSION['nombre'],
                    'servicios' => Servicio::all(),
                    'alertas' => $alertas
                ]);
            }

            if (empty($alertas)) {
                $servicio->eliminar();
                header('Location: /servicios');
            }
        }

        
    }
}
