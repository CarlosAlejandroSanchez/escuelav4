<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Factory\AppFactory;
use Illuminate\Database\Capsule\Manager as DB;

require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../config/database.php';

// Instantiate app
$app = AppFactory::create();
$app->setBasePath("/escuelav4/api/index.php");

// Add Error Handling Middleware
$app->addErrorMiddleware(true, false, false);

$app->post('/login', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $login = DB::table('miembros')->where('usuario', $data->usuario)->first();

    $msg = new stdClass();

    if($login)
    {
        if($login->contra == $data->contraseña)
        {
            $msg->acceso = 1;
            $msg->mensaje = "Bienvenido: " . $login->nombre . " " . $login->apellido;
            $msg->usuario = $login->usuario;
        }
        else {
            $msg->acceso = 0;
            $msg->mensaje = "contraseña incorrecta";
        }
    }
    else {
        $msg->mensaje = "cuenta y/o contraseña incorrectas";
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->get('/logear/{usuario}', function (Request $request, Response $response, array $args) {

    $msg = "Procesando...";

    require_once '../functions.php';

    $_SESSION['user'] = $args['usuario'];
    echo'<meta http-equiv="Refresh" content="0;url=../../../index.php">';

    $response->getBody()->write($msg);
    return $response;
});

$app->post('/añadir', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $msg = new stdClass();

    if($data->nombre == "" || $data->apellido == "" || $data->apellido2 == "")
    {
        $msg->vacio = true;
        $msg->mensaje = "Faltan datos";
    }
    else {
        $msg->vacio = false;

        $usuario = $data->nombre.$data->apellido;
        $usuario = str_replace(" ", "", $usuario);

        $validar = DB::table('miembros')->where('usuario',$usuario)->first();

        if(!$validar)
        {
            $msg->validar = false;

            $nombreC = $data->nombre . " " . $data->nombre2;
            $apellidoC =  $data->apellido . " " . $data->apellido2;
            $registrar = DB::table('miembros')->insertGetId(
                ['usuario' => $usuario, 'contra' => "escuela123", 'rol' => '2', 'nombre' => $nombreC, 'apellido' => $apellidoC]
            );

            $msg->mensaje = "Alumno: " . $nombreC . " " . $apellidoC . " registado con éxito";
        }
        else{
            $msg->validar = true;
            $msg->mensaje = "Ese alumno ya ha sido regstrado";
        }
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/calificacion/{id_usuario}', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $cal = DB::table('calificaciones')->where('miembros_id_miembros', $args['id_usuario'])->first();

    $msg = new stdClass();

    if($cal)
    {
        $msg->existe = true;
        $msg->esp = $cal->español;
        $msg->mate = $cal->Mate;
        $msg->hist = $cal->historia;
        $msg->prom = ($cal->español + $cal->Mate + $cal->historia)/3;
    }
    else {
        $msg->existe = false;
        $msg->mensaje = "Aún no tiene calificaciones";
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/alumnos', function (Request $request, Response $response, array $args) {

    $alumnos = DB::table('miembros')->select(['id_miembros', 'nombre', 'apellido'])->where('rol',"<>",1)->orderBy('apellido')->get();

    $msg = new stdClass();

    if($alumnos)
    {
        $msg->alumnos = true;
        $msg->nombres = $alumnos;
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/actualizar', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $msg = new stdClass();

    if($data->esp == "" || $data->hist == "" || $data->mate == "")
    {
        $msg->mensaje = "Faltan datos";
    }
    else
    {
        $verificar_alumno = DB::table('calificaciones')->where('miembros_id_miembros', $data->id)->first();

        if($verificar_alumno)
        {
            $nueva_calificación = DB::table('calificaciones')
            ->where('miembros_id_miembros', $data->id)
            ->update(['español' => $data->esp, 'Mate' => $data->mate, 'historia' => $data->hist]);

            if($nueva_calificación)
            {
                $msg->mensaje = "Calificación del alumno actualizada";
            }
            else
            {
                $msg->mensaje = "Ese alumno ya tiene esas calificaciones, por favor, seleccione caificaciones diferentes";
            }
        }
        else
        {
            $nueva_calificación = DB::table('calificaciones')->insert(
                ['miembros_id_miembros' => $data->id, 'español'=>$data->esp, 'historia'=>$data->hist, 'Mate'=>$data->mate]
            );

            if($nueva_calificación)
            {
                $msg->mensaje = "Calificaciones añadidas";
            }
        }
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

$app->post('/eliminar', function (Request $request, Response $response, array $args) {

    $data = json_decode($request->getBody()->getContents(), false);

    $msg = new stdClass();

    $eliminar = DB::table('calificaciones')->where('miembros_id_miembros', $data->id)->delete();

    if($eliminar)
    {
        $msg->mensaje = "Calificaciones eliminadas";
    }
    else {
        $msg->mensaje = "Ese alumno no cuenta con calificaciones y no puede ser eliminado";
    }

    $response->getBody()->write(json_encode($msg));
    return $response;
});

// Run application
$app->run();