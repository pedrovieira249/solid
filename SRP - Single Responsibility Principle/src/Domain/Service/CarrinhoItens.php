<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Domain\Service;

use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;

final class CarrinhoItens
{
    public function __construct(
        private Carrinho $carrinho
    ) {}

    public function addItem(Item $item): void
    {
        $itens = $this->getItens();

        if ($this->getStatusPedido() !== EnumStatus::ABERTO) {
            throw new \DomainException('Não é possível adicionar itens a um pedido que não está aberto.');
        }

        array_push($itens, $item);
        $this->setItens($itens);
        $this->recalcularValorTotal();

    }

    public function removerItem(Item $item): void
    {
        $itens = $this->getItens();

        if ($this->getStatusPedido() !== EnumStatus::ABERTO) {
            throw new \DomainException('Não é possível remover itens de um pedido que não está aberto.');
        }

        $index = array_search($item, $itens, true);
        if ($index !== false) {
            unset($itens[$index]);
            $this->setItens($itens);
            $this->recalcularValorTotal();
        }

    }

    private function setItens(array $itens): void
    {
        $this->carrinho->getDadosPedido()->setItens($itens);
    }

    private function getStatusPedido(): EnumStatus
    {
        return $this->carrinho->getDadosPedido()->getStatus();
    }

    private function getItens(): array
    {
        return $this->carrinho->getDadosPedido()->getItens();
    }

    private function recalcularValorTotal(): void
    {
        $itens = $this->getItens();
        $valorTotal = (new CalculadoraPedido($itens, $this->carrinho->getDadosPedido()->getEntrega()))->calcularValorTotal();
        $this->carrinho->getDadosPedido()->setValorTotal($valorTotal);
    }
}
