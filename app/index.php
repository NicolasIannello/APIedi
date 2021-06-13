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
    require __DIR__ . '/DB/accesoDatos.php';

    $app = AppFactory::create();

    $app->addErrorMiddleware(true,true,true);

    $app->add( function (Request $request, RequestHandlerInterface $handler): Response{
        $response = $handler->handle($request);
        $requestHeaders = $request->getHeaderLine('Access-Control-Request-Headers');

        $response = $response->withHeader('Access-Control-Allow-Origin', '*');
        $response = $response->withHeader('Access-Control-Allow-Methods', 'get,post,option');
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

    $app->get('/login/{user}/{contra}', \UsuarioController::class . ':obtenerTodos');

    $app->run();

?>