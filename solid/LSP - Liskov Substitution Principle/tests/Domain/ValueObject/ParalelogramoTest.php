<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Paralelogramo;
use PHPUnit\Framework\TestCase;

class ParalelogramoTest extends TestCase
{
    public function test_getLargura_retorna_valor_informado(): void
    {
        $paralelogramo = new Paralelogramo(6.0, 4.0);

        $this->assertSame(6.0, $paralelogramo->getLargura());
    }

    public function test_getAltura_retorna_valor_informado(): void
    {
        $paralelogramo = new Paralelogramo(6.0, 4.0);

        $this->assertSame(4.0, $paralelogramo->getAltura());
    }

    public function test_calcularArea_retorna_base_vezes_altura(): void
    {
        $paralelogramo = new Paralelogramo(6.0, 4.0);

        $this->assertSame(24.0, $paralelogramo->calcularArea());
    }

    public function test_calcularArea_com_valores_float(): void
    {
        $paralelogramo = new Paralelogramo(3.5, 2.0);

        $this->assertSame(7.0, $paralelogramo->calcularArea());
    }
}
