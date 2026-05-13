<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Service;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Contract\EmailServiceInterface;
use SrpSingleresponsibilityprinciple\Domain\Entity\Carrinho;
use SrpSingleresponsibilityprinciple\Domain\Entity\Cliente;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumStatus;
use SrpSingleresponsibilityprinciple\Domain\Service\NotificarPedido;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class NotificarPedidoTest extends TestCase
{
    private function criarCarrinhoComStatus(EnumStatus $status): Carrinho
    {
        $endereco = new Endereco('Rua A', 1, 'Bairro', 'Cidade', 'Estado', '00000-000');
        $cliente  = new Cliente(1, 'João', 'joao@example.com', '11999999999', $endereco);
        $carrinho = new Carrinho($cliente);

        $carrinho->getDadosPedido()->setStatus($status);

        return $carrinho;
    }

    public function testNotificarStatusPendenteEnviaEmail(): void
    {
        $carrinho     = $this->criarCarrinhoComStatus(EnumStatus::PENDENTE);
        $emailService = $this->createMock(EmailServiceInterface::class);

        $emailService->expects($this->once())
            ->method('send')
            ->with($this->callback(fn(Email $e) => str_contains($e->getAssunto(), 'Pendente')));

        (new NotificarPedido($carrinho, $emailService))->notificar();
    }

    public function testNotificarStatusFinalizadoEnviaEmail(): void
    {
        $carrinho     = $this->criarCarrinhoComStatus(EnumStatus::FINALIZADO);
        $emailService = $this->createMock(EmailServiceInterface::class);

        $emailService->expects($this->once())
            ->method('send')
            ->with($this->callback(fn(Email $e) => str_contains($e->getAssunto(), 'Finalizado')));

        (new NotificarPedido($carrinho, $emailService))->notificar();
    }

    public function testNotificarStatusCanceladoEnviaEmail(): void
    {
        $carrinho     = $this->criarCarrinhoComStatus(EnumStatus::CANCELADO);
        $emailService = $this->createMock(EmailServiceInterface::class);

        $emailService->expects($this->once())
            ->method('send')
            ->with($this->callback(fn(Email $e) => str_contains($e->getAssunto(), 'Cancelado')));

        (new NotificarPedido($carrinho, $emailService))->notificar();
    }

    public function testNotificarStatusAbertoNaoEnviaEmail(): void
    {
        $carrinho     = $this->criarCarrinhoComStatus(EnumStatus::ABERTO);
        $emailService = $this->createMock(EmailServiceInterface::class);

        $emailService->expects($this->never())->method('send');

        (new NotificarPedido($carrinho, $emailService))->notificar();
    }

    public function testNotificarSemClienteLancaExcecao(): void
    {
        $emailService = $this->createStub(EmailServiceInterface::class);

        $this->expectException(\InvalidArgumentException::class);

        (new NotificarPedido(new Carrinho(null), $emailService))->notificar();
    }
}
