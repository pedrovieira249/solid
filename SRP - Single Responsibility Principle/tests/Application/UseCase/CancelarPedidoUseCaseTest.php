<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\CancelarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\CriarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class CancelarPedidoUseCaseTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testExecutarAlteraStatusParaCancelado(): void
    {
        $carrinho     = $this->criarCarrinho();
        $emailService = $this->createMock(EmailServiceInterface::class);
        $emailService->expects($this->exactly(2))->method('send');

        $pedido = (new CriarPedidoUseCase($emailService))->executar(1, $carrinho);
        (new CancelarPedidoUseCase($emailService))->executar($pedido);

        $this->assertSame(EnumStatus::CANCELADO, $carrinho->getDadosPedido()->getStatus());
    }

    public function testExecutarNaoPodeCancelarPedidoJaCancelado(): void
    {
        $carrinho     = $this->criarCarrinho();
        $emailService = $this->createStub(EmailServiceInterface::class);

        $carrinho->getDadosPedido()->setStatus(EnumStatus::CANCELADO);
        $pedido = new \SrpSingleresponsibilityprinciple\Domain\Entity\Pedido(1, $carrinho);

        $this->expectException(\DomainException::class);

        (new CancelarPedidoUseCase($emailService))->executar($pedido);
    }
}
