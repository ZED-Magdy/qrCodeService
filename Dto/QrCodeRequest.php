<?php

namespace Dto;

use Symfony\Component\Validator\Constraints as Assert;

class QrCodeRequest
{
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private ?string $sellerName;
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private ?string $taxRecord;
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     * @Assert\DateTime(format="Y-m-d h:i A")
     */
    private ?string $bookingDate;
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private ?string $total;
    /**
     * @Assert\NotBlank
     * @Assert\NotNull
     */
    private ?string $vat;

    /**
     * @param string|null $sellerName
     * @param string|null $taxRecord
     * @param string|null $bookingDate
     * @param string|null $total
     * @param string|null $vat
     */
    public function __construct(?string $sellerName, ?string $taxRecord, ?string $bookingDate, ?string $total, ?string $vat)
    {
        $this->sellerName = $sellerName;
        $this->taxRecord = $taxRecord;
        $this->bookingDate = $bookingDate;
        $this->total = $total;
        $this->vat = $vat;
    }

    /**
     * @return string
     */
    public function getSellerName(): string
    {
        return $this->sellerName;
    }

    /**
     * @return string
     */
    public function getTaxRecord(): string
    {
        return $this->taxRecord;
    }

    /**
     * @return string
     */
    public function getBookingDate(): string
    {
        return $this->bookingDate;
    }

    /**
     * @return string
     */
    public function getTotal(): string
    {
        return $this->total;
    }

    /**
     * @return string
     */
    public function getVat(): string
    {
        return $this->vat;
    }


}