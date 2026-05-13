<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Retangulo;

class CalcularAreaRetangulo
{
    public function calcularArea(Retangulo $retangulo): float
    {
        return $retangulo->getLargura() * $retangulo->getAltura();
    }
}
