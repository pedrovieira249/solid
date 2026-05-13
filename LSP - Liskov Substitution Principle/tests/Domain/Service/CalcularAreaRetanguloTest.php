<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\Service;

use LspLiskovsubstitutionprinciple\Domain\Service\CalcularAreaRetangulo;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Retangulo;
use PHPUnit\Framework\TestCase;

class CalcularAreaRetanguloTest extends TestCase
{
    public function test_calcularArea_retorna_largura_vezes_altura(): void
    {
        $service   = new CalcularAreaRetangulo();
        $retangulo = new Retangulo(7.0, 3.0);

        $this->assertSame(21.0, $service->calcularArea($retangulo));
    }

    public function test_calcularArea_com_dimensoes_float(): void
    {
        $service   = new CalcularAreaRetangulo();
        $retangulo = new Retangulo(1.5, 2.0);

        $this->assertSame(3.0, $service->calcularArea($retangulo));
    }
}
