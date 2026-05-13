<?php

declare(strict_types=1);

namespace SrpSingleresponsibilityprinciple\Tests\Domain\ValueObject;

use PHPUnit\Framework\TestCase;
use SrpSingleresponsibilityprinciple\Domain\ValueObject\Endereco;

class EnderecoTest extends TestCase
{
    private function criarEnderecoValido(): Endereco
    {
        return new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', 'Estado D', '12345-678');
    }

    public function testGettersRetornamValoresCorretos(): void
    {
        $endereco = new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', 'Estado D', '12345-678', 'Apto 1');

        $this->assertSame('Rua A', $endereco->getRua());
        $this->assertSame(123, $endereco->getNumero());
        $this->assertSame('Bairro B', $endereco->getBairro());
        $this->assertSame('Cidade C', $endereco->getCidade());
        $this->assertSame('Estado D', $endereco->getEstado());
        $this->assertSame('12345-678', $endereco->getCep());
        $this->assertSame('Apto 1', $endereco->getComplemento());
    }

    public function testComplementoNullPorPadrao(): void
    {
        $endereco = $this->criarEnderecoValido();

        $this->assertNull($endereco->getComplemento());
    }

    public function testRuaVaziaLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Endereco('', 123, 'Bairro B', 'Cidade C', 'Estado D', '12345-678'));
    }

    public function testBairroVazioLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Endereco('Rua A', 123, '', 'Cidade C', 'Estado D', '12345-678'));
    }

    public function testCidadeVaziaLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Endereco('Rua A', 123, 'Bairro B', '', 'Estado D', '12345-678'));
    }

    public function testEstadoVazioLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', '', '12345-678'));
    }

    public function testCepVazioLancaExcecao(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        (new Endereco('Rua A', 123, 'Bairro B', 'Cidade C', 'Estado D', ''));
    }
}
