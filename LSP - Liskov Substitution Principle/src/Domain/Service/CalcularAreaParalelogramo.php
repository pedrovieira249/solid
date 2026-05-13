<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Paralelogramo;

class CalcularAreaParalelogramo
{
    public function calcularArea(Paralelogramo $paralelogramo): float
    {
        return $paralelogramo->getLargura() * $paralelogramo->getAltura();
    }
}
