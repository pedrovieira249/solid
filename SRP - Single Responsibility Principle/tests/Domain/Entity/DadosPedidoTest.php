<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\DadosPedido;
use SrpSingleresponsibilityprinciple\Domain\Entity\Entregas;
use SrpSingleresponsibilityprinciple\Domain\Entity\Item;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumTipoEntregas;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class DadosPedidoTest extends TestCase
{
    public function testValoresPadrao(): void
    {
        $dados = new DadosPedido();

        $this->assertSame([], $dados->getItens());
        $this->assertSame(0.0, $dados->getValorTotal());
        $this->assertSame(EnumStatus::ABERTO, $dados->getStatus());
        $this->assertNull($dados->getEntrega());
    }

    public function testSetItensEGetItens(): void
    {
        $dados = new DadosPedido();
        $itens = [new Item(1, 'Prod', 1, 10.0)];

        $dados->setItens($itens);

        $this->assertSame($itens, $dados->getItens());
    }

    public function testSetValorTotalEGetValorTotal(): void
    {
        $dados = new DadosPedido();

        $dados->setValorTotal(99.99);

        $this->assertSame(99.99, $dados->getValorTotal());
    }

    public function testSetStatusEGetStatus(): void
    {
        $dados = new DadosPedido();

        $dados->setStatus(EnumStatus::PENDENTE);

        $this->assertSame(EnumStatus::PENDENTE, $dados->getStatus());
    }

    public function testSetEntregaEGetEntrega(): void
    {
        $dados    = new DadosPedido();
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $entrega  = new Entregas(EnumTipoEntregas::NORMAL, $endereco, 15);

        $dados->setEntrega($entrega);

        $this->assertSame($entrega, $dados->getEntrega());
    }
}
