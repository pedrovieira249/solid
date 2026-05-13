<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaQuadrado;

class Quadrado extends PoligonoQuadrilateros
{
    public function __construct(float $lado)
    {
        parent::__construct($lado, $lado);
    }

    public function calcularArea(): float
    {
        return (new CalcularAreaQuadrado())->calcularArea($this);
    }
}
