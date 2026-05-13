<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Entregas;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumTipoEntregas;
use SrpSingleresponsibilityprinciple\Domain\Service\CalculadoraPedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class CalculadoraPedidoTest extends TestCase
{
    private function criarEndereco(): Endereco
    {
        return new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
    }

    public function testListaVaziaRetornaZero(): void
    {
        $calculadora = new CalculadoraPedido([]);

        $this->assertSame(0.0, $calculadora->calcularValorTotal());
    }

    public function testCalculaValorTotalSemEntrega(): void
    {
        $itens = [
            new Item(1, 'Produto 1', 2, 50.0),  // 100
            new Item(2, 'Produto 2', 1, 20.0),  // 20
        ];

        $calculadora = new CalculadoraPedido($itens);

        $this->assertSame(120.0, $calculadora->calcularValorTotal());
    }

    public function testCalculaValorTotalComEntrega(): void
    {
        $itens   = [new Item(1, 'Produto 1', 1, 100.0)];
        $entrega = new Entregas(EnumTipoEntregas::EXPRESSA, $this->criarEndereco(), 30);

        $calculadora = new CalculadoraPedido($itens, $entrega);

        $this->assertSame(130.0, $calculadora->calcularValorTotal());
    }

    public function testCalculaValorTotalComEntregaPadrao(): void
    {
        $itens   = [new Item(1, 'Produto 1', 1, 50.0)];
        $entrega = new Entregas(EnumTipoEntregas::NORMAL, $this->criarEndereco()); // valor padrão = 10

        $calculadora = new CalculadoraPedido($itens, $entrega);

        $this->assertSame(60.0, $calculadora->calcularValorTotal());
    }
}
