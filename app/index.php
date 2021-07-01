<?php
    error_reporting(-1);
    ini_set('display_errors', 1);

    use Psr\Http\Message\ResponseInterface as Response;
    use Psr\Http\Message\ServerRequestInterface as Request;
    use Psr\Http\Server\RequestHandlerInterface;
    use Slim\Factory\AppFactory;
    use Slim\Routing\RouteCollectorProxy;
    use Slim\Routing\RouteContext;

    require __DIR__ . '/../vendor/autoload.php';
    require __DIR__ . '/controllers/usuarioControllers.php';
    require __DIR__ . '/controllers/turnosControllers.php';
    require __DIR__ . '/controllers/serviciosControllers.php';
    require __DIR__ . '/DB/accesoDatos.php';
    require __DIR__ . '/entidades/usuario.php';
    require __DIR__ . '/entidades/turno.php';

    $app = AppFactory::create();

    $app->addErrorMiddleware(true,true,true);

    $app->add( function (Request $request, RequestHandlerInterface $handler): Response{
        $response = $handler->handle($request);
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'get,post,option,delete');
        $response = $response->withHeader('Access-Control-Allow-Headers', $requestHeaders);

        return $response;
    });

    $app->get('/hello/{name}/{apellido}', function (Request $request, Response $response, array $args) {
        $name = $args['name'];
        $apellido = $args['apellido'];
        $response->getBody()->write("Hello, $name $apellido");
        return $response;
    });
/*
    $app->post('/post[/]', function (Request $request, Response $response, array $args) {
        FORM-DATA
        $valor= $request->getParsedBody();
        $response->getBody()->write($valor["user"]." ".$valor["contra"]);
        return $response;
        JSON
        $valor=$request->getBody();
        $valor2=json_decode($valor);
        $response->getBody()->write($valor2);
        return $response;
    });*/

    $app->get('[/]', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Utilizando slim framework");
        return $response;
    });

    $app->post('/login[/]', \UsuarioController::class . ':obtenerTodos');

    $app->get('/servicios[/]', \ServicioController::class . ':ObtenerTodos');

    $app->group('/turno', function (RouteCollectorProxy $group) {
        $group->post('/crear[/]', \TurnoController::class . ':crearTurnos' );
        $group->get('/cargar[/]', \TurnoController::class . ':cargarTurnos' );
        $group->post('/eliminar[/]', \TurnoController::class . ':eliminarTurno' );
    });

    $app->run();
?>