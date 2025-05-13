<?php

namespace App\ValueObjects;

class Price
{
    public function __construct(protected int $rial)
    {
    }

    public static function fromRial(int|float $rial): static
    {
        return new static((int)round($rial));
    }

    public function __toString(): string
    {
        return $this->rial();
    }

    public function rial(): int
    {
        return $this->rial;
    }

    public function formatted(): string
    {
        return number_format($this->toman()) . ' تومان';
    }

    public function toman(): float
    {
        return $this->rial / 10;
    }

    public function add(Price $other): static
    {
        return new static($this->rial + $other->rial);
    }

    public function subtract(Price $other): static
    {
        return new static($this->rial - $other->rial);
    }
}
