<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Email;

class EmailTest extends TestCase
{
    public function testGettersRetornamValoresCorretos(): void
    {
        $email = new Email('joao@example.com', 'Pedido Confirmado', 'Seu pedido foi confirmado.');

        $this->assertSame('joao@example.com', $email->getEmail());
        $this->assertSame('Pedido Confirmado', $email->getAssunto());
        $this->assertSame('Seu pedido foi confirmado.', $email->getMensagem());
    }
}
