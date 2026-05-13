<?php

declare(strict_types=1);

namespace LspLiskovsubstitutionprinciple\Tests\Domain\ValueObject;

use LspLiskovsubstitutionprinciple\Domain\ValueObject\Paralelogramo;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\PoligonoQuadrilateros;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Quadrado;
use LspLiskovsubstitutionprinciple\Domain\ValueObject\Retangulo;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

/**
 * Valida o L do SOLID: qualquer subtipo de PoligonoQuadrilateros
 * pode substituir a classe base sem alterar o comportamento esperado.
 */
class LiskovSubstitutionTest extends TestCase
{
    #[DataProvider('provedorPoligonos')]
    public function test_todo_subtipo_implementa_calcularArea(PoligonoQuadrilateros $poligono, float $areaEsperada): void
    {
        $this->assertSame($areaEsperada, $poligono->calcularArea());
    }

    #[DataProvider('provedorPoligonos')]
    public function test_todo_subtipo_expoe_getLargura(PoligonoQuadrilateros $poligono, float $areaEsperada): void
    {
        $this->assertIsFloat($poligono->getLargura());
    }

    #[DataProvider('provedorPoligonos')]
    public function test_todo_subtipo_expoe_getAltura(PoligonoQuadrilateros $poligono, float $areaEsperada): void
    {
        $this->assertIsFloat($poligono->getAltura());
    }

    #[DataProvider('provedorPoligonos')]
    public function test_calcularArea_nunca_retorna_valor_negativo(PoligonoQuadrilateros $poligono, float $areaEsperada): void
    {
        $this->assertGreaterThan(0.0, $poligono->calcularArea());
    }

    public static function provedorPoligonos(): array
    {
        return [
            'Retangulo 5x3'        => [new Retangulo(5.0, 3.0), 15.0],
            'Quadrado lado 4'      => [new Quadrado(4.0), 16.0],
            'Paralelogramo 6x4'    => [new Paralelogramo(6.0, 4.0), 24.0],
        ];
    }
}
