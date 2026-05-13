<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Entity;

final class Carrinho
{
    public function __construct(
        private ?Cliente $cliente,
        private DadosPedido $dadosPedido = new DadosPedido(),
    ) {
    }

    public function getCliente(): ?Cliente
    {
        return $this->cliente;
    }

    public function getDadosPedido(): DadosPedido
    {
        return $this->dadosPedido;
    }
}
