<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Retangulo;
use PHPUnit\Framework\TestCase;

class RetanguloTest extends TestCase
{
    public function test_getLargura_retorna_valor_informado(): void
    {
        $retangulo = new Retangulo(5.0, 3.0);

        $this->assertSame(5.0, $retangulo->getLargura());
    }

    public function test_getAltura_retorna_valor_informado(): void
    {
        $retangulo = new Retangulo(5.0, 3.0);

        $this->assertSame(3.0, $retangulo->getAltura());
    }

    public function test_calcularArea_retorna_largura_vezes_altura(): void
    {
        $retangulo = new Retangulo(5.0, 3.0);

        $this->assertSame(15.0, $retangulo->calcularArea());
    }

    public function test_calcularArea_com_valores_float(): void
    {
        $retangulo = new Retangulo(2.5, 4.0);

        $this->assertSame(10.0, $retangulo->calcularArea());
    }

    public function test_calcularArea_com_largura_igual_a_altura(): void
    {
        $retangulo = new Retangulo(4.0, 4.0);

        $this->assertSame(16.0, $retangulo->calcularArea());
    }
}
