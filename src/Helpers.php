<?php
namespace App;
use App\Dto\QrCodeRequest;
use DateTime;
use DateTimeInterface;
use Psr\Http\Message\ResponseInterface;
use Salla\ZATCA\GenerateQrCode;
use Salla\ZATCA\Tags\InvoiceDate;
use Salla\ZATCA\Tags\InvoiceTaxAmount;
use Salla\ZATCA\Tags\InvoiceTotalAmount;
use Salla\ZATCA\Tags\Seller;
use Salla\ZATCA\Tags\TaxNumber;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class Helpers
{

    /**
     * @param ConstraintViolationListInterface $errors
     * @param ResponseInterface $response
     * @return ResponseInterface
     */
    public static function createErrorResponse(ConstraintViolationListInterface $errors, ResponseInterface $response): ResponseInterface
    {
        $errs = [];
        foreach ($errors as $error) {
            $errs[$error->getPropertyPath()] = $error->getMessage();
        }
        $response->getBody()->write(json_encode(['errors' => $errs]));
        return $response->withStatus(422)
            ->withHeader('Content-Type', 'application/json');
    }

    public static function base64Encoded(QrCodeRequest $dto): string
    {
        return GenerateQrCode::fromArray([
            new Seller($dto->getSellerName()),
            new TaxNumber($dto->getTaxRecord()),
            new InvoiceDate((new DateTime($dto->getBookingDate()))->format(DateTimeInterface::ATOM)),
            new InvoiceTotalAmount($dto->getTotal()),
            new InvoiceTaxAmount($dto->getVat())
        ])->toBase64();
    }

    /**
     * @param QrCodeRequest $dto
     * @return string
     * @throws \Exception
     */
    public static function renderedQrCode(QrCodeRequest $dto): string
    {
        return GenerateQrCode::fromArray([
            new Seller($dto->getSellerName()),
            new TaxNumber($dto->getTaxRecord()),
            new InvoiceDate((new DateTime($dto->getBookingDate()))->format(DateTimeInterface::ATOM)),
            new InvoiceTotalAmount($dto->getTotal()),
            new InvoiceTaxAmount($dto->getVat())
        ])->render();
    }
}