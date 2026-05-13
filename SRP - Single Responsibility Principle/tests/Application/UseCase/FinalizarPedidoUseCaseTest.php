<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\CriarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\FinalizarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class FinalizarPedidoUseCaseTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testExecutarAlteraStatusParaFinalizado(): void
    {
        $carrinho     = $this->criarCarrinho();
        $emailService = $this->createMock(EmailServiceInterface::class);
        $emailService->expects($this->exactly(2))->method('send');

        $pedido = (new CriarPedidoUseCase($emailService))->executar(1, $carrinho);
        (new FinalizarPedidoUseCase($emailService))->executar($pedido);

        $this->assertSame(EnumStatus::FINALIZADO, $carrinho->getDadosPedido()->getStatus());
    }

    public function testExecutarNaoPodeFinalizarPedidoJaCancelado(): void
    {
        $carrinho     = $this->criarCarrinho();
        $emailService = $this->createStub(EmailServiceInterface::class);

        $carrinho->getDadosPedido()->setStatus(EnumStatus::CANCELADO);
        $pedido = new \SrpSingleresponsibilityprinciple\Domain\Entity\Pedido(1, $carrinho);

        $this->expectException(\DomainException::class);

        (new FinalizarPedidoUseCase($emailService))->executar($pedido);
    }
}
