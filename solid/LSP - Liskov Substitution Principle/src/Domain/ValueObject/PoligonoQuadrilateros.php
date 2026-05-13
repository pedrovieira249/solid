<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Domain\ValueObject;

abstract class PoligonoQuadrilateros
{
    public function __construct(
        private float $largura,
        private float $altura
    ) {

    }

    public function getLargura(): float
    {
        return $this->largura;
    }

    public function getAltura(): float
    {
        return $this->altura;
    }

    abstract public function calcularArea(): float;
}
