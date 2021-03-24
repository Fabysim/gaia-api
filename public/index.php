<?php

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Selective\BasePath\BasePathDetector;
use Slim\Factory\AppFactory;
use Slim\Routing\RouteCollectorProxy as RouteCollectorProxy;

require __DIR__ . '/../src/config.php';
require __DIR__ . '/../src/class/MyPDO.class.php';
require __DIR__ . '/../vendor/autoload.php';
require __DIR__ . '/../src/middlewares/JsonBodyParserMiddleware.class.php';
require __DIR__ . '/../src/controllers/Index.class.php';
require __DIR__ . '/../src/controllers/Config.php';
require __DIR__ . '/../src/controllers/WaitingLabels.php';
require __DIR__ . '/../src/controllers/Methods.php';
require __DIR__ . '/../src/controllers/Signals.php';
require __DIR__ . '/../src/controllers/Operations.php';
require __DIR__ . '/../src/controllers/Ranking.php';
require __DIR__ . '/../src/controllers/RunMethod.php';

$app = AppFactory::create();

$app->add(new Tuupola\Middleware\JwtAuthentication([
    "secret" => "thisIsACustomKey458SecretA",
    "header" => "Authorization",
    "secure" => true,
    "relaxed" => ["localhost","127.0.0.1","10.10.14.96"],
    "ignore" => ["/demo","/waiting","/methods","/measures","/config","/signals","/operations","/ranking","/run"],
    "algorithm" => ["HS256"],
    "error" => function ($response, $arguments) {
        $data["status"] = "error";
        $data["message"] = $arguments["message"];
        return $response
            ->withHeader("Content-Type", "application/json")
            ->getBody()->write(json_encode($data, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT));
    }
]));
//+++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

$basePath = (new BasePathDetector($_SERVER))->getBasePath();
$app->setBasePath($basePath);
$callableResolver = $app->getCallableResolver();


$customErrorHandler = function (
    ServerRequestInterface $request,
    Throwable $exception,
    bool $displayErrorDetails,
    bool $logErrors,
    bool $logErrorDetails
) use ($app) {
    $response = array();
    $response['status'] = 'error';
    $response['code '] = $exception->getCode();
    $payload = ['status' => 'error', 'code' => $exception->getCode(), 'message' => $exception->getMessage()];

    $response = $app->getResponseFactory()->createResponse();
    $response->getBody()->write(
        json_encode($payload, JSON_UNESCAPED_UNICODE)
    );

    return $response
        ->withHeader('Content-Type', 'application/json')
        ->withHeader('Access-Control-Allow-Origin', '*')
        ->withHeader('Access-Control-Allow-Headers', '*')
        ->withHeader('Access-Control-Allow-Methods', '*')
        ->withStatus($exception->getCode());
};



if ($debug === false) {
    $errorMiddleware = $app->addErrorMiddleware(true, true, true);
    $errorMiddleware->setDefaultErrorHandler($customErrorHandler);
}

$app->any('/', 'IndexController');

$app->any('/config[/{id}]', 'ConfigController')->setName('config')->add(new JsonBodyParserMiddleware());
$app->any('/waiting[/{id}]', 'WaitingConditionController')->setName('waiting')->add(new JsonBodyParserMiddleware());
$app->any('/methods[/{id}]', 'MethodsController')->setName('methods')->add(new JsonBodyParserMiddleware());
$app->any('/signals[/{id}]', 'SignalsController')->setName('signals')->add(new JsonBodyParserMiddleware());
$app->any('/operations[/{id}]', 'OperationsController')->setName('operations')->add(new JsonBodyParserMiddleware());
$app->any('/ranking[/{id}]', 'RankingController')->setName('ranking')->add(new JsonBodyParserMiddleware());
$app->any('/run[/{id}]', 'RunMethodController')->setName('run')->add(new JsonBodyParserMiddleware());


$app->run();