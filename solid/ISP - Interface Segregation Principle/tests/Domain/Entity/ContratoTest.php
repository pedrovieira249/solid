<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Domain\Entity;

use IspInterfacesegregationprinciple\Domain\Contract\ContratoInterface;
use IspInterfacesegregationprinciple\Domain\Entity\Contrato;
use PHPUnit\Framework\TestCase;

class ContratoTest extends TestCase
{
    private Contrato $contrato;

    protected function setUp(): void
    {
        ob_start();
        $this->contrato = new Contrato();
        ob_end_clean();
    }

    public function testImplementaContratoInterface(): void
    {
        $this->assertInstanceOf(ContratoInterface::class, $this->contrato);
    }

    public function testSalvarRetornaTrue(): void
    {
        ob_start();
        $result = $this->contrato->salvar();
        ob_end_clean();

        $this->assertTrue($result);
    }
}
