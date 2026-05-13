<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

final class Pedido
{
    public function __construct(
        private readonly int $idPedido,
        private Carrinho $carrinho,
    ) {
        if ($this->carrinho->getCliente() === null) {
            throw new \InvalidArgumentException('O carrinho deve conter um cliente para criar um pedido.');
        }
    }

    public function getIdPedido(): int
    {
        return $this->idPedido;
    }

    public function getCarrinho(): Carrinho
    {
        return $this->carrinho;
    }
}
