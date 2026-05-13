<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaParalelogramo;

class Paralelogramo extends PoligonoQuadrilateros
{
    public function calcularArea(): float
    {
        return (new CalcularAreaParalelogramo())->calcularArea($this);
    }
}
