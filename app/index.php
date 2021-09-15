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
    require __DIR__ . '/controllers/localidadesControllers.php';
    require __DIR__ . '/controllers/clienteControllers.php';
    require __DIR__ . '/DB/accesoDatos.php';
    require __DIR__ . '/entidades/usuario.php';
    require __DIR__ . '/entidades/turno.php';
    require __DIR__ . '/entidades/servicio.php';
    require __DIR__ . '/entidades/localidades.php';
    require __DIR__ . '/entidades/cliente.php';

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

    $app->get('[/]', function (Request $request, Response $response, array $args) {
        $response->getBody()->write("Utilizando slim framework");
        return $response;
    });

    $app->group('/usuario', function (RouteCollectorProxy $group) {
        $group->post('/login[/]', \UsuarioController::class . ':obtenerTodos' );
        $group->post('/crear/empresa[/]', \UsuarioController::class . ':crearEmpresa' );
        $group->post('/crear/cliente[/]', \UsuarioController::class . ':crearCliente' );
    });

    $app->get('/servicios[/]', \ServicioController::class . ':ObtenerTodos');

    $app->group('/turno', function (RouteCollectorProxy $group) {
        $group->post('/crear[/]', \TurnoController::class . ':crearTurnos' );
        $group->post('/cargar[/]', \TurnoController::class . ':cargarTurnos' );
        $group->post('/eliminar[/]', \TurnoController::class . ':eliminarTurno' );
        $group->post('/turnoCliente[/]', \TurnoController::class . ':cargarCliente' );
        $group->post('/clienteCargar[/]', \TurnoController::class . ':tablaCliente' );
        $group->post('/traernom[/]', \TurnoController::class . ':traernom' );
    });

    $app->group('/cliente', function (RouteCollectorProxy $group) {
        $group->post('/cargar[/]', \ClienteController::class . ':cargar' );
        $group->post('/buscarservicios[/]', \ClienteController::class . ':buscarservicios' );
        $group->post('/diaservicio[/]', \ClienteController::class . ':diaservicio' );
        $group->post('/traernom[/]', \ClienteController::class . ':traernom' );
        $group->post('/horarios[/]', \ClienteController::class . ':horarios' );
        $group->post('/crear[/]', \ClienteController::class . ':crear' );
    });

    $app->get('/localidades[/]', \LocalidadesController::class . ':ObtenerTodos');

    $app->run();
?>