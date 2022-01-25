<?php

use App\Application;
use App\Dto\QrCodeRequest;
use App\Helpers;
use App\Middleware\JsonBodyParserMiddleware;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

require __DIR__ . '/vendor/autoload.php';
$app = Application::getInstance();

$app->post('/', function (Request $request, Response $response, $args) use ($app) {
    $body = $request->getParsedBody();
    $dto = new QrCodeRequest($body['sellerName'], $body['taxRecord'], $body['bookingDate'], $body['total'], $body['vat']);
    $validator = $app->getContainer()->get(ValidatorInterface::class);
    $errors = $validator->validate($dto);
    if($errors->count() > 0){
        return Helpers::createErrorResponse($errors, $response);
    }
    if($request->hasHeader('Accept') && 'text/html' == $request->getHeader('Accept')[0]){
        $resp = Helpers::renderedQrCode($dto);
    }else{
        $resp = Helpers::base64Encoded($dto);
    }
    $response->getBody()->write(json_encode(['data' => $resp]));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new JsonBodyParserMiddleware());

$app->run();

