<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Quadrado;
use PHPUnit\Framework\TestCase;

class QuadradoTest extends TestCase
{
    public function test_getLargura_retorna_o_lado_informado(): void
    {
        $quadrado = new Quadrado(4.0);

        $this->assertSame(4.0, $quadrado->getLargura());
    }

    public function test_getAltura_retorna_o_lado_informado(): void
    {
        $quadrado = new Quadrado(4.0);

        $this->assertSame(4.0, $quadrado->getAltura());
    }

    public function test_largura_e_altura_sao_sempre_iguais(): void
    {
        $quadrado = new Quadrado(6.0);

        $this->assertSame($quadrado->getLargura(), $quadrado->getAltura());
    }

    public function test_calcularArea_retorna_lado_ao_quadrado(): void
    {
        $quadrado = new Quadrado(4.0);

        $this->assertSame(16.0, $quadrado->calcularArea());
    }

    public function test_calcularArea_com_lado_float(): void
    {
        $quadrado = new Quadrado(2.5);

        $this->assertSame(6.25, $quadrado->calcularArea());
    }
}
