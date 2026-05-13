<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\Service\StatusDoPedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class StatusDoPedidoTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testPendenciarAlteraStatusParaPendente(): void
    {
        $carrinho = $this->criarCarrinho();

        (new StatusDoPedido($carrinho))->pendenciar();

        $this->assertSame(EnumStatus::PENDENTE, $carrinho->getDadosPedido()->getStatus());
    }

    public function testFinalizarAPartirDeAbertoAlteraStatusParaFinalizado(): void
    {
        $carrinho = $this->criarCarrinho();

        (new StatusDoPedido($carrinho))->finalizar();

        $this->assertSame(EnumStatus::FINALIZADO, $carrinho->getDadosPedido()->getStatus());
    }

    public function testFinalizarAPartirDePendenteAlteraStatusParaFinalizado(): void
    {
        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::PENDENTE);

        (new StatusDoPedido($carrinho))->finalizar();

        $this->assertSame(EnumStatus::FINALIZADO, $carrinho->getDadosPedido()->getStatus());
    }

    public function testCancelarAlteraStatusParaCancelado(): void
    {
        $carrinho = $this->criarCarrinho();

        (new StatusDoPedido($carrinho))->cancelar();

        $this->assertSame(EnumStatus::CANCELADO, $carrinho->getDadosPedido()->getStatus());
    }

    public function testPendenciarLancaExcecaoSeStatusNaoEAberto(): void
    {
        $this->expectException(\DomainException::class);

        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::PENDENTE);

        (new StatusDoPedido($carrinho))->pendenciar();
    }

    public function testFinalizarLancaExcecaoSeStatusEFinalizado(): void
    {
        $this->expectException(\DomainException::class);

        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::FINALIZADO);

        (new StatusDoPedido($carrinho))->finalizar();
    }

    public function testFinalizarLancaExcecaoSeStatusECancelado(): void
    {
        $this->expectException(\DomainException::class);

        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::CANCELADO);

        (new StatusDoPedido($carrinho))->finalizar();
    }

    public function testCancelarLancaExcecaoSeJaCancelado(): void
    {
        $this->expectException(\DomainException::class);

        $carrinho = $this->criarCarrinho();
        $carrinho->getDadosPedido()->setStatus(EnumStatus::CANCELADO);

        (new StatusDoPedido($carrinho))->cancelar();
    }
}
