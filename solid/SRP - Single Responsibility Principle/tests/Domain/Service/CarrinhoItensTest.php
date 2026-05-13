<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\Service\CarrinhoItens;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class CarrinhoItensTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testAddItemAdicionaItemAoCarrinho(): void
    {
        $carrinho      = $this->criarCarrinho();
        $carrinhoItens = new CarrinhoItens($carrinho);
        $item          = new Item(1, 'Produto 1', 2, 50.0);

        $carrinhoItens->addItem($item);

        $this->assertCount(1, $carrinho->getDadosPedido()->getItens());
    }

    public function testAddItemRecalculaValorTotal(): void
    {
        $carrinho      = $this->criarCarrinho();
        $carrinhoItens = new CarrinhoItens($carrinho);

        $carrinhoItens->addItem(new Item(1, 'Produto 1', 2, 50.0)); // 100
        $carrinhoItens->addItem(new Item(2, 'Produto 2', 1, 30.0)); // 30

        $this->assertSame(130.0, $carrinho->getDadosPedido()->getValorTotal());
    }

    public function testRemoverItemRemoveDoCarrinho(): void
    {
        $carrinho      = $this->criarCarrinho();
        $carrinhoItens = new CarrinhoItens($carrinho);
        $item          = new Item(1, 'Produto 1', 2, 50.0);

        $carrinhoItens->addItem($item);
        $carrinhoItens->removerItem($item);

        $this->assertCount(0, $carrinho->getDadosPedido()->getItens());
    }

    public function testRemoverItemRecalculaValorTotal(): void
    {
        $carrinho      = $this->criarCarrinho();
        $carrinhoItens = new CarrinhoItens($carrinho);
        $item1         = new Item(1, 'Produto 1', 1, 100.0);
        $item2         = new Item(2, 'Produto 2', 1, 40.0);

        $carrinhoItens->addItem($item1);
        $carrinhoItens->addItem($item2);
        $carrinhoItens->removerItem($item2);

        $this->assertSame(100.0, $carrinho->getDadosPedido()->getValorTotal());
    }

    public function testAddItemLancaExcecaoQuandoPedidoNaoEstaAberto(): void
    {
        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::PENDENTE);

        $carrinhoItens = new CarrinhoItens($carrinho);

        $this->expectException(\DomainException::class);
        $carrinhoItens->addItem(new Item(1, 'Produto', 1, 10.0));
    }

    public function testRemoverItemLancaExcecaoQuandoPedidoNaoEstaAberto(): void
    {
        $carrinho = $this->criarCarrinho();
        $item     = new Item(1, 'Produto', 1, 10.0);

        $carrinhoItens = new CarrinhoItens($carrinho);
        $carrinhoItens->addItem($item);

        $carrinho->getDadosPedido()->setStatus(EnumStatus::PENDENTE);

        $this->expectException(\DomainException::class);
        $carrinhoItens->removerItem($item);
    }
}
