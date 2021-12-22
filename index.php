<?php

use DI\Bridge\Slim\Bridge;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\DocParser;
use Dto\QrCodeRequest;
use Middleware\JsonBodyParserMiddleware;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Slim\Factory\AppFactory;
use DI\Container;
use Symfony\Component\Translation\Translator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\Context\ExecutionContextFactory;
use Symfony\Component\Validator\Mapping\Factory\LazyLoadingMetadataFactory;
use Symfony\Component\Validator\Mapping\Loader\AnnotationLoader;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use \Symfony\Component\Validator\Validator\RecursiveValidator;
require __DIR__ . '/vendor/autoload.php';

$container = new Container();
AppFactory::setContainer($container);
$app = AppFactory::create();
$container->set(ValidatorInterface::class, function (ContainerInterface $container){
    return new RecursiveValidator(
                new ExecutionContextFactory(
                        new Translator('en')
                ),
                new LazyLoadingMetadataFactory(
                    new AnnotationLoader(
                        new AnnotationReader(
                            new DocParser()
                        )
                    )
                ),
                new ConstraintValidatorFactory()
    );
});
$app->post('/', function (Request $request, Response $response, $args) use ($container) {

    $body = $request->getParsedBody();
    $dto = new QrCodeRequest($body['sellerName'], $body['taxRecord'], $body['bookingDate'], $body['total'], $body['vat']);
    $validator = $container->get(ValidatorInterface::class);
    $errors = $validator->validate($dto);
    if($errors->count() > 0){
        $errs = [];
        foreach ($errors as $error) {
            $errs[$error->getPropertyPath()][] = $error->getMessage();
        }
        $response->getBody()->write(json_encode(['errors' => $errs]));
        return $response->withStatus(422)
            ->withHeader('Content-Type', 'application/json');
    }
    $base64Code = GenerateQrCode::fromArray([
        new Seller($dto->getSellerName()),
        new TaxNumber($dto->getTaxRecord()),
        new InvoiceDate((new DateTime($dto->getBookingDate()))->format(DateTimeInterface::ATOM)),
        new InvoiceTotalAmount($dto->getTotal()),
        new InvoiceTaxAmount($dto->getVat())
        ])->toBase64();

    $response->getBody()->write(json_encode(['data' => $base64Code]));
    return $response->withHeader('Content-Type', 'application/json');
})->add(new JsonBodyParserMiddleware());

$app->run();

