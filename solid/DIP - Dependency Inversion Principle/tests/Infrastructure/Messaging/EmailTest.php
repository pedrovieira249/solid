<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Tests\Infrastructure\Messaging;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\Email;
use PHPUnit\Framework\TestCase;

class EmailTest extends TestCase
{
    private Email $email;

    protected function setUp(): void
    {
        $this->email = new Email();
    }

    public function test_implementa_mensageiro_interface(): void
    {
        $this->assertInstanceOf(MensageiroInterface::class, $this->email);
    }

    public function test_enviar_mensagem_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando mensagem para contato@example\.com via Email/');

        $resultado = $this->email->enviarMensagem('contato@example.com', 'Olá!');

        $this->assertTrue($resultado);
    }

    public function test_enviar_token_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando token para contato@example\.com via Email/');

        $resultado = $this->email->enviarToken('contato@example.com', '123456');

        $this->assertTrue($resultado);
    }

    public function test_enviar_mensagem_exibe_contato_e_mensagem(): void
    {
        $this->expectOutputRegex('/contato@example\.com.*Mensagem de teste/s');

        $this->email->enviarMensagem('contato@example.com', 'Mensagem de teste');
    }

    public function test_enviar_token_exibe_contato_e_token(): void
    {
        $this->expectOutputRegex('/contato@example\.com.*987654/s');

        $this->email->enviarToken('contato@example.com', '987654');
    }
}
