<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaQuadrado;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Quadrado;
use PHPUnit\Framework\TestCase;

class CalcularAreaQuadradoTest extends TestCase
{
    public function test_calcularArea_retorna_lado_ao_quadrado(): void
    {
        $service  = new CalcularAreaQuadrado();
        $quadrado = new Quadrado(5.0);

        $this->assertSame(25.0, $service->calcularArea($quadrado));
    }

    public function test_calcularArea_com_lado_float(): void
    {
        $service  = new CalcularAreaQuadrado();
        $quadrado = new Quadrado(3.0);

        $this->assertSame(9.0, $service->calcularArea($quadrado));
    }
}
