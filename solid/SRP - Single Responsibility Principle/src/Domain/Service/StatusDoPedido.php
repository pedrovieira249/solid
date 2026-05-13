<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Service;

use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;

final class StatusDoPedido
{
    public function __construct(
        private Carrinho $carrinho,
    ) {
    }

    public function pendenciar(): void
    {
        if ($this->carrinho->getDadosPedido()->getStatus() !== EnumStatus::ABERTO) {
            throw new \DomainException('Apenas pedidos abertos podem ser pendenciados.');
        }

        $this->carrinho->getDadosPedido()->setStatus(EnumStatus::PENDENTE);
    }

    public function finalizar(): void
    {
        if ($this->carrinho->getDadosPedido()->getStatus() !== EnumStatus::ABERTO && $this->carrinho->getDadosPedido()->getStatus() !== EnumStatus::PENDENTE) {
            throw new \DomainException('Apenas pedidos abertos ou pendentes podem ser finalizados.');
        }

        $this->carrinho->getDadosPedido()->setStatus(EnumStatus::FINALIZADO);
    }

    public function cancelar(): void
    {
        if ($this->carrinho->getDadosPedido()->getStatus() === EnumStatus::CANCELADO) {
            throw new \DomainException('O pedido já está cancelado.');
        }

        $this->carrinho->getDadosPedido()->setStatus(EnumStatus::CANCELADO);
    }
}
