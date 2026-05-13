<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaRetangulo;

class Retangulo extends PoligonoQuadrilateros
{
    public function calcularArea(): float
    {
        return (new CalcularAreaRetangulo())->calcularArea($this);
    }
}
