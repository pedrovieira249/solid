<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Tests\Infrastructure\Messaging;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;
use DipDependencyInversionPrinciple\Infrastructure\Messaging\Sms;
use PHPUnit\Framework\TestCase;

class SmsTest extends TestCase
{
    private Sms $sms;

    protected function setUp(): void
    {
        $this->sms = new Sms();
    }

    public function test_implementa_mensageiro_interface(): void
    {
        $this->assertInstanceOf(MensageiroInterface::class, $this->sms);
    }

    public function test_enviar_mensagem_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando mensagem para 5531987654321 via SMS/');

        $resultado = $this->sms->enviarMensagem('5531987654321', 'Olá!');

        $this->assertTrue($resultado);
    }

    public function test_enviar_token_retorna_true(): void
    {
        $this->expectOutputRegex('/Enviando token para 5531987654321 via SMS/');

        $resultado = $this->sms->enviarToken('5531987654321', '123456');

        $this->assertTrue($resultado);
    }

    public function test_enviar_mensagem_exibe_contato_e_mensagem(): void
    {
        $this->expectOutputRegex('/5531987654321.*Mensagem de teste/s');

        $this->sms->enviarMensagem('5531987654321', 'Mensagem de teste');
    }

    public function test_enviar_token_exibe_contato_e_token(): void
    {
        $this->expectOutputRegex('/5531987654321.*987654/s');

        $this->sms->enviarToken('5531987654321', '987654');
    }
}
