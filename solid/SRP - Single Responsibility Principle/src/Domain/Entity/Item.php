<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

final class Item
{
    public function __construct(
        private readonly int $idProduto,
        private readonly string $nomeProduto,
        private readonly int $quantidade,
        private readonly float $valorUnitario
    ) {}

    public function getIdProduto(): int
    {
        return $this->idProduto;
    }

    public function getNomeProduto(): string
    {
        return $this->nomeProduto;
    }

    public function getQuantidade(): int
    {
        return $this->quantidade;
    }

    public function getValorUnitario(): float
    {
        return $this->valorUnitario;
    }
}
