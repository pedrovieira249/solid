<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Application\Service;

use IspInterfacesegregationprinciple\Application\Service\Notificacao;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoInterface;
use IspInterfacesegregationprinciple\Domain\Contract\NotificacaoRecipientInterface;
use PHPUnit\Framework\MockObject\Stub;
use PHPUnit\Framework\TestCase;

class NotificacaoTest extends TestCase
{
    private Stub&NotificacaoRecipientInterface $recipient;
    private Notificacao $notificacao;

    protected function setUp(): void
    {
        $this->recipient = $this->createStub(NotificacaoRecipientInterface::class);
        $this->recipient->method('getNome')->willReturn('Maria');
        $this->recipient->method('getEmail')->willReturn('maria@example.com');

        $this->notificacao = new Notificacao();
    }

    public function testImplementaNotificacaoInterface(): void
    {
        $this->assertInstanceOf(NotificacaoInterface::class, $this->notificacao);
    }

    public function testEnviarNotificacaoRetornaTrue(): void
    {
        ob_start();
        $result = $this->notificacao->enviarNotificacao($this->recipient, 'Bem-vindo!');
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testEnviarNotificacaoExibeNomeDoRecipient(): void
    {
        $this->expectOutputRegex('/Maria/');

        $this->notificacao->enviarNotificacao($this->recipient, 'Seu pedido foi confirmado.');
    }

    public function testEnviarNotificacaoExibeEmailDoRecipient(): void
    {
        $this->expectOutputRegex('/maria@example\.com/');

        $this->notificacao->enviarNotificacao($this->recipient, 'Sua senha foi alterada.');
    }

    public function testEnviarNotificacaoNaoDependeDaEntidadeUsuario(): void
    {
        $outroRecipient = $this->createStub(NotificacaoRecipientInterface::class);
        $outroRecipient->method('getNome')->willReturn('Carlos');
        $outroRecipient->method('getEmail')->willReturn('carlos@example.com');

        ob_start();
        $result = $this->notificacao->enviarNotificacao($outroRecipient, 'Olá, Carlos!');
        ob_end_clean();

        $this->assertTrue($result);
    }
}
