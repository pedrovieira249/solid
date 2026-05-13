<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Domain\Service;

use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;
use IspInterfacesegregationprinciple\Domain\Service\Log;
use PHPUnit\Framework\TestCase;

class LogTest extends TestCase
{
    public function testImplementaLogInterface(): void
    {
        $this->assertInstanceOf(LogInterface::class, new Log());
    }

    public function testRegistrarLogExibeMensagem(): void
    {
        $log = new Log();

        $this->expectOutputString("Log registrado: Operação realizada.\n");

        $log->registrarLog('Operação realizada.');
    }

    public function testRegistrarLogExibeMensagemComTextoVariado(): void
    {
        $log = new Log();

        $this->expectOutputRegex('/Log registrado: .+/');

        $log->registrarLog('Usuário autenticado com sucesso.');
    }
}
