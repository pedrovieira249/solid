<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\DadosPedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class CarrinhoTest extends TestCase
{
    private function criarCliente(): Cliente
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');

        return new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);
    }

    public function testGetClienteRetornaClienteInformado(): void
    {
        $cliente  = $this->criarCliente();
        $carrinho = new Carrinho($cliente);

        $this->assertSame($cliente, $carrinho->getCliente());
    }

    public function testGetDadosPedidoRetornaInstanciaDedfaultDeDadosPedido(): void
    {
        $carrinho = new Carrinho($this->criarCliente());

        $this->assertInstanceOf(DadosPedido::class, $carrinho->getDadosPedido());
    }

    public function testClienteNullPermitido(): void
    {
        $carrinho = new Carrinho(null);

        $this->assertNull($carrinho->getCliente());
    }
}
