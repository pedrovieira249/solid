<?php

declare(strict_types=1);

namespace IspInterfacesegregationprinciple\Tests\Domain\Entity;

use IspInterfacesegregationprinciple\Domain\Contract\LeadInterface;
use IspInterfacesegregationprinciple\Domain\Contract\LogInterface;
use IspInterfacesegregationprinciple\Domain\Entity\Lead;
use PHPUnit\Framework\TestCase;

class LeadTest extends TestCase
{
    private LogInterface $logger;
    private Lead $lead;

    protected function setUp(): void
    {
        $this->logger = $this->createStub(LogInterface::class);

        ob_start();
        $this->lead = new Lead('Maria', 'maria@example.com', $this->logger);
        ob_end_clean();
    }

    public function testImplementaLeadInterface(): void
    {
        $this->assertInstanceOf(LeadInterface::class, $this->lead);
    }

    public function testGerarRetornaTrue(): void
    {
        ob_start();
        $result = $this->lead->gerar();
        ob_end_clean();

        $this->assertTrue($result);
    }

    public function testGerarDelegaLogParaLogInterface(): void
    {
        $logger = $this->createMock(LogInterface::class);
        $logger->expects($this->once())
            ->method('registrarLog')
            ->with($this->stringContains('Maria'));

        ob_start();
        $lead = new Lead('Maria', 'maria@example.com', $logger);
        $lead->gerar();
        ob_end_clean();
    }

    public function testNaoImplementaLogInterface(): void
    {
        $this->assertNotInstanceOf(LogInterface::class, $this->lead);
    }
}
