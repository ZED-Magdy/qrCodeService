<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Slim\Factory\AppFactory;

require __DIR__ . '/vendor/autoload.php';
class JsonBodyParserMiddleware implements MiddlewareInterface
{
    public function process(Request $request, RequestHandler $handler): Response
    {
        $contentType = $request->getHeaderLine('Content-Type');

        if (strstr($contentType, 'application/json')) {
            $contents = json_decode(file_get_contents('php://input'), true);
            if (json_last_error() === JSON_ERROR_NONE) {
                $request = $request->withParsedBody($contents);
            }
        }

        return $handler->handle($request);
    }
}
$app = AppFactory::create();

$app->post('/', function (Request $request, Response $response, $args) {

    $body = $request->getParsedBody();
    $base64Code = GenerateQrCode::fromArray([
        new Seller($body['sellerName']),
        new TaxNumber($body['taxRecord']),
        new InvoiceDate((new DateTime($body['bookingDate']))->format(DateTime::ATOM)),
        new InvoiceTotalAmount($body['total']),
        new InvoiceTaxAmount($body['vat'])
        ])->toBase64();

    $response->getBody()->write(json_encode(['data' => $base64Code]));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new JsonBodyParserMiddleware());

$app->run();

