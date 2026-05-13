<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;

class DadosPedido
{
    public function __construct(
        private array $itens = [],
        private float $valorTotal = 0.0,
        private EnumStatus $status = EnumStatus::ABERTO,
        private ?Entregas $entrega = null
    ) {}

    public function getEntrega(): ?Entregas
    {
        return $this->entrega;
    }

    public function setEntrega(?Entregas $entrega): void
    {
        $this->entrega = $entrega;
    }

    public function getItens(): array
    {
        return $this->itens;
    }

    public function setItens(array $itens): void
    {
        $this->itens = $itens;
    }

    public function getStatus(): EnumStatus
    {
        return $this->status;
    }

    public function setStatus(EnumStatus $status): void
    {
        $this->status = $status;
    }

    public function getValorTotal(): float
    {
        return $this->valorTotal;
    }

    public function setValorTotal(float $valorTotal): void
    {
        $this->valorTotal = $valorTotal;
    }
}
