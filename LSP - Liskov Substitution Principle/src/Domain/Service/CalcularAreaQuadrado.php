<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Quadrado;

class CalcularAreaQuadrado
{
    public function calcularArea(Quadrado $quadrado): float
    {
        return $quadrado->getLargura() ** 2;
    }
}
