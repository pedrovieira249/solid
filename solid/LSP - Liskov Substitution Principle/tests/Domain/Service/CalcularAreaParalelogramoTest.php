<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaParalelogramo;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Paralelogramo;
use PHPUnit\Framework\TestCase;

class CalcularAreaParalelogramoTest extends TestCase
{
    public function test_calcularArea_retorna_base_vezes_altura(): void
    {
        $service       = new CalcularAreaParalelogramo();
        $paralelogramo = new Paralelogramo(8.0, 5.0);

        $this->assertSame(40.0, $service->calcularArea($paralelogramo));
    }

    public function test_calcularArea_com_dimensoes_float(): void
    {
        $service       = new CalcularAreaParalelogramo();
        $paralelogramo = new Paralelogramo(4.5, 2.0);

        $this->assertSame(9.0, $service->calcularArea($paralelogramo));
    }
}
