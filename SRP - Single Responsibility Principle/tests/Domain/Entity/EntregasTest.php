<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\Entity;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\Entity\Entregas;
use SrpSingleresponsibilityprinciple\Domain\Enum\EnumTipoEntregas;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class EntregasTest extends TestCase
{
    private function criarEndereco(): Endereco
    {
        return new Endereco('Rua A', 10, 'Bairro', 'Cidade', 'Estado', '00000-000');
    }

    public function testGettersRetornamValoresCorretos(): void
    {
        $endereco = $this->criarEndereco();
        $entrega  = new Entregas(EnumTipoEntregas::EXPRESSA, $endereco, 25);

        $this->assertSame(EnumTipoEntregas::EXPRESSA, $entrega->getTipoEntrega());
        $this->assertSame($endereco, $entrega->getEnderecoEntrega());
        $this->assertSame(25, $entrega->getValorEntrega());
    }

    public function testValorEntregaPadraoEDez(): void
    {
        $endereco = $this->criarEndereco();
        $entrega  = new Entregas(EnumTipoEntregas::NORMAL, $endereco);

        $this->assertSame(10, $entrega->getValorEntrega());
    }
}
