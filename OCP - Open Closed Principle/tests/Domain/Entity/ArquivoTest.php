<?php

declare(strict_types=1);

namespace OcpOpenclosedprinciple\Tests\Domain\Entity;

use OcpOpenclosedprinciple\Domain\Entity\Arquivo;
use OcpOpenclosedprinciple\Domain\Enum\EnumTipoArquivo;
use PHPUnit\Framework\TestCase;

class ArquivoTest extends TestCase
{
    private string $recursos;

    protected function setUp(): void
    {
        $this->recursos = realpath(__DIR__ . '/../../../src/resources');
    }

    public function testInstanciaComArquivoCsvExistente(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertInstanceOf(Arquivo::class, $arquivo);
    }

    public function testInstanciaComArquivoTxtExistente(): void
    {
        $arquivo = new Arquivo('dados.txt', EnumTipoArquivo::TXT, $this->recursos);

        $this->assertInstanceOf(Arquivo::class, $arquivo);
    }

    public function testGetNome(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertSame('dados.csv', $arquivo->getNome());
    }

    public function testGetTipo(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertSame(EnumTipoArquivo::CSV, $arquivo->getTipo());
    }

    public function testGetDiretorio(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertSame($this->recursos, $arquivo->getDiretorio());
    }

    public function testGetCaminhoCompleto(): void
    {
        $arquivo = new Arquivo('dados.csv', EnumTipoArquivo::CSV, $this->recursos);

        $this->assertSame($this->recursos . DIRECTORY_SEPARATOR . 'dados.csv', $arquivo->getCaminhoCompleto());
    }

    public function testLancaExcecaoQuandoNomeSemExtensao(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("O nome do arquivo deve conter uma extensão.");

        (new Arquivo('dados', EnumTipoArquivo::CSV, $this->recursos));
    }

    public function testLancaExcecaoQuandoTipoIncompativel(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("A extensão do arquivo deve ser compatível com o tipo especificado.");

        (new Arquivo('dados.txt', EnumTipoArquivo::CSV, $this->recursos));
    }

    public function testLancaExcecaoQuandoArquivoNaoExiste(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage("não foi encontrado");

        (new Arquivo('inexistente.csv', EnumTipoArquivo::CSV, $this->recursos));
    }
}
