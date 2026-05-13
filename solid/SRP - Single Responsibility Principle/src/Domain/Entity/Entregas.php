<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

use SrpSingleresponsibilityprinciple\Domain\Enum\EnumTipoEntregas;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class Entregas
{
    public function __construct(
        private EnumTipoEntregas $tipoEntrega,
        private Endereco $enderecoEntrega,
        private int $valorEntrega = 10,
    ) {}

    public function getTipoEntrega(): EnumTipoEntregas
    {
        return $this->tipoEntrega;
    }

    public function getEnderecoEntrega(): Endereco
    {
        return $this->enderecoEntrega;
    }

    public function getValorEntrega(): int
    {
        return $this->valorEntrega;
    }
}
