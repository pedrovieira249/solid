<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Tests\Infrastructure\Messaging;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\WhatsApp;
use PHPUnit\Framework\TestCase;

class WhatsAppTest extends TestCase
{
    private WhatsApp $whatsApp;

    protected function setUp(): void
    {
        $this->whatsApp = new WhatsApp();
    }

    public function test_implementa_mensageiro_interface(): void
    {
        $this->assertInstanceOf(MensageiroInterface::class, $this->whatsApp);
    }

    public function test_enviar_mensagem_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando mensagem para 5531987654322 via WhatsApp/');

        $resultado = $this->whatsApp->enviarMensagem('5531987654322', 'Olá!');

        $this->assertTrue($resultado);
    }

    public function test_enviar_token_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando token para 5531987654322 via WhatsApp/');

        $resultado = $this->whatsApp->enviarToken('5531987654322', '123456');

        $this->assertTrue($resultado);
    }

    public function test_enviar_mensagem_exibe_contato_e_mensagem(): void
    {
        $this->expectOutputRegex('/5531987654322.*Mensagem de teste/s');

        $this->whatsApp->enviarMensagem('5531987654322', 'Mensagem de teste');
    }

    public function test_enviar_token_exibe_contato_e_token(): void
    {
        $this->expectOutputRegex('/5531987654322.*987654/s');

        $this->whatsApp->enviarToken('5531987654322', '987654');
    }
}
