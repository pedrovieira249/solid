<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Application\UseCase;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Application\UseCase\CriarPedidoUseCase;
use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Entity\Pedido;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class CriarPedidoUseCaseTest extends TestCase
{
    private function criarCarrinho(): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);

        return new Carrinho($cliente);
    }

    public function testExecutarCriaPedidoEAlteraStatusParaPendente(): void
    {
        $carrinho     = $this->criarCarrinho();
        $emailService = $this->createMock(EmailServiceInterface::class);
        $emailService->expects($this->once())->method('send');

        $pedido = (new CriarPedidoUseCase($emailService))->executar(1, $carrinho);

        $this->assertInstanceOf(Pedido::class, $pedido);
        $this->assertSame(1, $pedido->getIdPedido());
        $this->assertSame(EnumStatus::PENDENTE, $carrinho->getDadosPedido()->getStatus());
    }

    public function testExecutarSemClienteLancaExcecao(): void
    {
        $emailService = $this->createMock(EmailServiceInterface::class);
        $emailService->expects($this->never())->method('send');

        $this->expectException(\InvalidArgumentException::class);

        (new CriarPedidoUseCase($emailService))->executar(1, new Carrinho(null));
    }
}
