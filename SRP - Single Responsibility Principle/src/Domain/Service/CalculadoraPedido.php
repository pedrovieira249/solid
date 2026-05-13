<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Service;

use SrpSingleresponsibilityprinciple\Domain\Entity\Entregas;

final class CalculadoraPedido
{
    public function __construct(
        private array $itens,
        private ?Entregas $entrega = null
    ) {}

    public function calcularValorTotal(): float
    {
        $valorTotal = 0.0;

        foreach ($this->itens as $item) {
            $valorTotal += $item->getValorUnitario() * $item->getQuantidade();
        }

        $valorTotal += ($this->entrega ? $this->entrega->getValorEntrega() : 0);

        return $valorTotal;
    }
}
