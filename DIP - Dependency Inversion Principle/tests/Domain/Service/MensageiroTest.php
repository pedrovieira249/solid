<?php

declare(strict_types=1);

namespace DipDependencyInversionPrinciple\Tests\Domain\Service;

use DipDependencyInversionPrinciple\Domain\Contract\MensageiroInterface;
use DipDependencyInversionPrinciple\Domain\Service\Mensageiro;
use PHPUnit\Framework\TestCase;

class MensageiroTest extends TestCase
{
    public function test_enviar_mensagem_delega_para_o_canal(): void
    {
        $canal = $this->createMock(MensageiroInterface::class);
        $canal->expects($this->once())
            ->method('enviarMensagem')
            ->with('destino@example.com', 'Olá!')
            ->willReturn(true);

        $mensageiro = new Mensageiro($canal);

        $resultado = $mensageiro->enviarMensagem('destino@example.com', 'Olá!');

        $this->assertTrue($resultado);
    }

    public function test_enviar_token_delega_para_o_canal(): void
    {
        $canal = $this->createMock(MensageiroInterface::class);
        $canal->expects($this->once())
            ->method('enviarToken')
            ->with('destino@example.com', '123456')
            ->willReturn(true);

        $mensageiro = new Mensageiro($canal);

        $resultado = $mensageiro->enviarToken('destino@example.com', '123456');

        $this->assertTrue($resultado);
    }

    public function test_enviar_mensagem_retorna_false_quando_canal_falha(): void
    {
        $canal = $this->createStub(MensageiroInterface::class);
        $canal->method('enviarMensagem')->willReturn(false);

        $mensageiro = new Mensageiro($canal);

        $resultado = $mensageiro->enviarMensagem('destino@example.com', 'Olá!');

        $this->assertFalse($resultado);
    }

    public function test_enviar_token_retorna_false_quando_canal_falha(): void
    {
        $canal = $this->createStub(MensageiroInterface::class);
        $canal->method('enviarToken')->willReturn(false);

        $mensageiro = new Mensageiro($canal);

        $resultado = $mensageiro->enviarToken('destino@example.com', '123456');

        $this->assertFalse($resultado);
    }

    public function test_aceita_qualquer_implementacao_de_mensageiro_interface(): void
    {
        $canalA = $this->createStub(MensageiroInterface::class);
        $canalB = $this->createStub(MensageiroInterface::class);

        $canalA->method('enviarMensagem')->willReturn(true);
        $canalB->method('enviarMensagem')->willReturn(true);

        $mensageiroA = new Mensageiro($canalA);
        $mensageiroB = new Mensageiro($canalB);

        $this->assertTrue($mensageiroA->enviarMensagem('a@example.com', 'msg'));
        $this->assertTrue($mensageiroB->enviarMensagem('b@example.com', 'msg'));
    }
}
