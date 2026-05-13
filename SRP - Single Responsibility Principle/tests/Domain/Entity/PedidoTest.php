<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\Pedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class PedidoTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testCriacaoPedidoGuardaIdECarrinho(): void
    {
        $carrinho = $this->criarCarrinho();
        $pedido   = new Pedido(1, $carrinho);

        $this->assertSame(1, $pedido->getIdPedido());
        $this->assertSame($carrinho, $pedido->getCarrinho());
    }

    public function testCriacaoPedidoSemClienteLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Pedido(1, new Carrinho(null)));
    }
}
