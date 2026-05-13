<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Infrastructure\Service;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;
use SrpSingleresponsibilityprinciple\Infrastructure\Service\EmailService;

class EmailServiceTest extends TestCase
{
    public function testSendRetornaTrue(): void
    {
        $email   = new Email('joao@example.com', 'Assunto Teste', 'Mensagem de teste.');
        $service = new EmailService();

        $this->expectOutputRegex('/.*/');
        $resultado = $service->send($email);

        $this->assertTrue($resultado);
    }

    public function testSendExibeEmailDestinatarioNasSaida(): void
    {
        $email   = new Email('maria@example.com', 'Assunto', 'Mensagem.');
        $service = new EmailService();

        $this->expectOutputRegex('/maria@example\.com/');
        $service->send($email);
    }

    public function testSendExibeAssuntoNaSaida(): void
    {
        $email   = new Email('teste@example.com', 'Pedido Confirmado', 'Mensagem.');
        $service = new EmailService();

        $this->expectOutputRegex('/Pedido Confirmado/');
        $service->send($email);
    }
}
